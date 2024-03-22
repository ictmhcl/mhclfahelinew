<?php

/**
 * This is the model class for table "transaction_logs".
 *
 * The followings are the available columns in table 'transaction_logs':
 * @property string $transaction_id
 * @property string $user_id
 * @property string $record_time
 * @property string $request_ip
 * @property string $request_url
 * @property string $controller_id
 * @property string $action_id
 *
 * The followings are the available model relations:
 * @property Audits[] $audits
 */
class TransactionLogs extends CActiveRecord
{

  const AUDIT_TABLE = "transaction_logs";
  public function afterConstruct() {
    $this->user_id = Yii::app()->user->id;
    $this->begin_time = microtime(true);
    $this->request_ip = Yii::app()->request->userHostAddress;
    $this->request_url = Yii::app()->request->hostInfo.Yii::app()->request->url;
    $this->controller_id = Yii::app()->controller->id;
    $this->action_id = Yii::app()->controller->action->id;
    $this->save();
  }


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TransactionLogs the static model class
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
		return 'transaction_logs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['user_id, begin_time, request_ip, request_url', 'required'],
			['user_id', 'length', 'max'=>11],
			['request_ip, controller_id, action_id', 'length', 'max'=>100],
			['begin_time, request_url', 'safe'],
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			['transaction_id, user_id, begin_time, request_ip, request_url, controller_id, action_id', 'safe', 'on'=>'search'],
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
			'audits' => [self::HAS_MANY, 'Audits', 'transaction_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'transaction_id' => 'Transaction',
			'user_id' => 'User',
			'begin_time' => 'Begin Time',
			'request_ip' => 'Request Ip',
			'request_url' => 'Request Url',
			'controller_id' => 'Controller',
			'action_id' => 'Action',
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

		$criteria->compare('transaction_id',$this->transaction_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('begin_time',$this->begin_time,true);
		$criteria->compare('request_ip',$this->request_ip,true);
		$criteria->compare('request_url',$this->request_url,true);
		$criteria->compare('controller_id',$this->controller_id,true);
		$criteria->compare('action_id',$this->action_id,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}
}