<?php

/**
 * This is the model class for table "audit_log_action_types".
 *
 * The followings are the available columns in table 'audit_log_action_types':
 * @property integer $audit_log_action_type_id
 * @property string $action_type_name
 */
class AuditLogActionTypes extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuditLogActionTypes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return CDbConnection database connection
	 */
	public function getDbConnection()
	{
		return Yii::app()->db_audit;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'audit_log_action_types';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['action_type_name', 'required'],
			['action_type_name', 'length', 'max'=>250],
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			['audit_log_action_type_id, action_type_name', 'safe', 'on'=>'search'],
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
			'audit_log_action_type_id' => 'Audit Log Action Type',
			'action_type_name' => 'Action Type Name',
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

		$criteria->compare('audit_log_action_type_id',$this->audit_log_action_type_id);
		$criteria->compare('action_type_name',$this->action_type_name,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}
}