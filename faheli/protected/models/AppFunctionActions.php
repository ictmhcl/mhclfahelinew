<?php

/**
 * This is the model class for table "app_function_actions".
 *
 * The followings are the available columns in table 'app_function_actions':
 * @property integer $app_action_id
 * @property integer $app_function_id
 */
class AppFunctionActions extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AppFunctionActions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'app_function_actions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['app_action_id, app_function_id', 'required'],
			['app_action_id, app_function_id', 'numerical', 'integerOnly'=>true],
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			['app_action_id, app_function_id', 'safe', 'on'=>'search'],
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
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'app_action_id' => 'App Action',
			'app_function_id' => 'App Function',
    ];
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('app_action_id',$this->app_action_id);
		$criteria->compare('app_function_id',$this->app_function_id);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}
}