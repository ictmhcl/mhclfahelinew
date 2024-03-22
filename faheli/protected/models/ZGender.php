<?php

/**
 * This is the model class for table "z_gender".
 *
 * The followings are the available columns in table 'z_gender':
 * @property integer $gender_id
 * @property string $name_english
 * @property string $name_dhivehi
 *
 * The followings are the available model relations:
 * @property ApplicationForms[] $applicationForms
 * @property ApplicationForms[] $applicationForms1
 * @property Persons[] $persons
 */
class ZGender extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'z_gender';
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
			['name_english', 'length', 'max'=>10],
			['name_dhivehi', 'length', 'max'=>50],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['gender_id, name_english, name_dhivehi', 'safe', 'on'=>'search'],
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
			'applicationForms' => [self::HAS_MANY, 'ApplicationForms', 'replacement_gender_id'],
			'applicationForms1' => [self::HAS_MANY, 'ApplicationForms', 'applicant_gender_id'],
			'persons' => [self::HAS_MANY, 'Persons', 'gender_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'gender_id' => 'Gender',
			'name_english' => 'Name English',
			'name_dhivehi' => 'Name Dhivehi',
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

		$criteria->compare('gender_id',$this->gender_id);
		$criteria->compare('name_english',$this->name_english,true);
		$criteria->compare('name_dhivehi',$this->name_dhivehi,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZGender the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
