<?php

/**
 * This is the model class for table "z_application_states".
 *
 * The followings are the available columns in table 'z_application_states':
 * @property integer $state_id
 * @property string $name_english
 * @property string $name_dhivehi
 * @property string $state_description
 *
 * The followings are the available model relations:
 * @property ApplicationForms[] $applicationForms
 */
class ZApplicationStates extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'z_application_states';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name_english, name_dhivehi, state_description', 'required'],
			['name_english, name_dhivehi', 'length', 'max'=>100],
			['state_description', 'length', 'max'=>255],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['state_id, name_english, name_dhivehi, state_description', 'safe', 'on'=>'search'],
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
			'applicationForms' => [self::HAS_MANY, 'ApplicationForms', 'state_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'state_id' => 'State',
			'name_english' => 'Name English',
			'name_dhivehi' => 'Name Dhivehi',
			'state_description' => 'State Description',
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

		$criteria->compare('state_id',$this->state_id);
		$criteria->compare('name_english',$this->name_english,true);
		$criteria->compare('name_dhivehi',$this->name_dhivehi,true);
		$criteria->compare('state_description',$this->state_description,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZApplicationStates the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
