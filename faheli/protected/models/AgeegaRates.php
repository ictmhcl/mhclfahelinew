<?php

/**
 * This is the model class for table "ageega_rates".
 *
 * The followings are the available columns in table 'ageega_rates':
 * @property integer $id
 * @property integer $gender_id
 * @property double $rate
 * @property string $from_date
 * @property string $till_date
 *
 * The followings are the available model relations:
 * @property ZGender $gender
 */
class AgeegaRates extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ageega_rates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['gender_id', 'required'],
			['gender_id', 'numerical', 'integerOnly'=>true],
			['rate', 'numerical'],
			['till_date', 'checkAfterFromDate'],
			['from_date, till_date', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, gender_id, rate, from_date, till_date', 'safe', 'on'=>'search'],
		];
	}

	public function checkAfterFromDate($attribute) {
		if (empty($this->$attribute))
			return;
		if ((new DateTime($this->$attribute)) < (new DateTime($this->from_date)))
			$this->addError($attribute, 'Applicable end date must be later than
			Applicable start date');
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'gender' => [self::BELONGS_TO, 'ZGender', 'gender_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'gender_id' => 'Gender',
			'rate' => 'Rate',
			'from_date' => 'Applicable Date',
			'till_date' => 'End Date',
		];
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('gender_id',$this->gender_id);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('till_date',$this->till_date,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AgeegaRates the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function behaviors() {
		return [
			// Classname => path to Class
				'ActiveRecordDateBehavior' =>
						'application.behaviors.ActiveRecordDateBehavior',
		];
	}

}
