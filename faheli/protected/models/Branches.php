<?php

/**
 * This is the model class for table "branches".
 *
 * The followings are the available columns in table 'branches':
 * @property integer $id
 * @property integer $organization_id
 * @property string $name_english
 * @property string $name_dhivehi
 *
 * The followings are the available model relations:
 * @property Organizations $organization
 * @property Users[] $users
 */
class Branches extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'branches';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['organization_id', 'numerical', 'integerOnly'=>true],
			['name_english, name_dhivehi', 'length', 'max'=>255],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, organization_id, name_english, name_dhivehi', 'safe', 'on'=>'search'],
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
			'organization' => [self::BELONGS_TO, 'Organizations', 'organization_id'],
			'users' => [self::HAS_MANY, 'Users', 'branch_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'organization_id' => 'Organization',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('organization_id',$this->organization_id);
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
	 * @return Branches the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
