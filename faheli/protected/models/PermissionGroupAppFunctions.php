<?php

/**
 * This is the model class for table "permission_group_app_functions".
 *
 * The followings are the available columns in table 'permission_group_app_functions':
 * @property integer $id
 * @property integer $permission_group_id
 * @property integer $app_function_id
 *
 * The followings are the available model relations:
 * @property AppFunctions $appFunction
 * @property PermissionGroups $permissionGroup
 */
class PermissionGroupAppFunctions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'permission_group_app_functions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['permission_group_id, app_function_id', 'required'],
			['permission_group_id, app_function_id', 'numerical', 'integerOnly'=>true],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, permission_group_id, app_function_id', 'safe', 'on'=>'search'],
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
			'appFunction' => [self::BELONGS_TO, 'AppFunctions', 'app_function_id'],
			'permissionGroup' => [self::BELONGS_TO, 'PermissionGroups', 'permission_group_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'permission_group_id' => 'Permission Group',
			'app_function_id' => 'App Function',
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
		$criteria->compare('permission_group_id',$this->permission_group_id);
		$criteria->compare('app_function_id',$this->app_function_id);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PermissionGroupAppFunctions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
