<?php

/**
 * This is the model class for table "app_functions".
 *
 * The followings are the available columns in table 'app_functions':
 * @property integer $id
 * @property string $name
 * @property string $description
 *
 * The followings are the available model relations:
 * @property AppActions[] $appActions
 * @property JobAppFunctions[] $jobAppFunctions
 * @property PermissionGroupAppFunctions[] $permissionGroupAppFunctions
 * @property Users[] $users
 */
class AppFunctions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'app_functions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name, description', 'required'],
			['name', 'length', 'max'=>50],
			['description', 'length', 'max'=>255],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, name, description', 'safe', 'on'=>'search'],
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
			'appActions' => [self::MANY_MANY, 'AppActions', 'app_function_actions(app_function_id, app_action_id)'],
			'jobAppFunctions' => [self::HAS_MANY, 'JobAppFunctions', 'app_function_id'],
			'permissionGroupAppFunctions' => [self::HAS_MANY, 'PermissionGroupAppFunctions', 'app_function_id'],
			'users' => [self::MANY_MANY, 'Users', 'user_permissions(app_function_id, user_id)'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AppFunctions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
