<?php

/**
 * This is the model class for table "person_login".
 *
 * The followings are the available columns in table 'person_login':
 * @property integer $person_id
 * @property integer $login_code
 * @property integer $mobile
 * @property string $issued_at
 *
 * The followings are the available model relations:
 * @property Persons $person
 */
class PersonLogin extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'person_login';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['person_id, login_code, mobile, issued_at', 'required'],
			['person_id, login_code, mobile', 'numerical', 'integerOnly'=>true],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'person' => [self::BELONGS_TO, 'Persons', 'person_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'person_id' => 'Person',
			'login_code' => 'Login Code',
			'mobile' => 'Logged in Mobile',
			'issued_at' => 'Issued At',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PersonLogin the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public static function generateLoginCode(Persons $person, $mobile) {
		$personLogin = self::model()->findByPk($person->id);
		if (empty($personLogin)) {
			$personLogin = new PersonLogin();
			$personLogin->person_id = $person->id;
		}
		$personLogin->mobile = $mobile;
		$personLogin->login_code = Helpers::generateCode(6);
		$personLogin->issued_at = Yii::app()->params['dateTime'];
		try {
			return $personLogin->save() ? $personLogin->login_code : false;
		} catch (CException $ex) {
			ErrorLog::exceptionLog($ex);

			return false;
		}

	}

	public static function loginPerson($personId, $loginCode) {
		$criteria = new CDbCriteria();
		$criteria->compare('person_id', $personId);
		$criteria->compare('login_code', $loginCode);
		/** @var PersonLogin $personLogin */
		$personLogin = self::model()->find($criteria);
		if (!empty($personLogin)) {
			$issuedAt = new DateTime($personLogin->issued_at);
			$validUntil = $issuedAt->add(new DateInterval('PT' .
					trim(Helpers::config('userCodeValidityPeriod') * 60) . 'S'));
			if (new DateTime(Yii::app()->params['dateTime']) < $validUntil) {
				return $personLogin->person;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

}
