<?php

/**
 * This is the model class for table "z_ageega_reasons".
 *
 * The followings are the available columns in table 'z_ageega_reasons':
 * @property integer $id
 * @property string $name_english
 * @property string $name_dhivehi
 *
 * The followings are the available model relations:
 * @property Ageega[] $ageegas
 */
class ZAgeegaReasons extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'z_ageega_reasons';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name_english, name_dhivehi', 'required'],
			['name_english, name_dhivehi', 'length', 'max'=>255],
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
			'ageegas' => [self::HAS_MANY, 'Ageega', 'reason'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name_english' => 'Name English',
			'name_dhivehi' => 'Name Dhivehi',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZAgeegaReasons the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}




}
