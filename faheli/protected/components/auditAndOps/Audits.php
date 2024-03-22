<?php

/**
 * This is the model class for table "audits".
 *
 * The followings are the available columns in table 'audits':
 * @property string $audit_id
 * @property string $user_id
 * @property string $record_time
 * @property string $request_ip
 * @property string $request_url
 * @property string $record_data
 * @property integer $crud_type
 * @property string $active_record_class
 * @property string $record_id
 * @property string $sql_command
 * @property string $transaction_id
 * @property string $controller_id
 * @property string $action_id
 * @property string $post_params
 * @property string $files_uploaded
 * @property string $get_params
 *
 * The followings are the available model relations:
 * @property TransactionLogs $transaction
 */
class Audits extends CActiveRecord
{

  const CRUD_CREATE = "Insert";
  const CRUD_UPDATE = "Update";
  const CRUD_UPDATE_BYPK = "Update by Pk";
  const CRUD_UPDATE_ALL = "Update All";
  const CRUD_DELETE = "Delete";
  const CRUD_DELETE_BYPK = "Delete by Pk";
  const CRUD_DELETE_ALL = "Delete All";
  const CRUD_DELETE_ALL_BYATTR = "Delete All by Attr";
  const CREATE_TABLE = "Create Table";
  const RENAME_TABLE = "Rename Table";
  const DROP_TABLE = "Drop Table";
  const TRUNCATE_TABLE = "Truncate Table";
  const ADD_COLUMN = "Add Column";
  const DROP_COLUMN = "Drop Column";
  const RENAME_COLUMN = "Rename Column";
  const ALTER_COLUMN = "Alter Column";
  const ADD_FK = "Add Foreign Key";
  const DROP_FK = "Drop Foreign Key";
  const CREATE_INDEX = "Create Index";
  const DROP_INDEX = "Drop Index";

  const AUDIT_TABLE = "audits";

  public function afterConstruct() {

    $this->user_id = Yii::app()->user->id;
    $this->record_time = microtime(true);
    $this->request_ip = Yii::app()->request->userHostAddress;
    $this->request_url = Yii::app()->request->hostInfo.Yii::app()->request->url;
    $this->controller_id = Yii::app()->controller->id;
    $this->action_id = Yii::app()->controller->action->id;
    $this->post_params = CJSON::encode($_POST);
    $this->files_uploaded = CJSON::encode($_FILES);
    $this->get_params = CJSON::encode($_GET);
    $this->sql_success = 0;
  }


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Audits the static model class
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
		return 'audits';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['crud_type, active_record_class, record_id, transaction_id, controller_id, action_id, post_params, get_params', 'required'],
			['crud_type', 'numerical', 'integerOnly'=>true],
			['user_id, record_id, transaction_id', 'length', 'max'=>10],
			['request_ip, active_record_class, controller_id, action_id, get_params', 'length', 'max'=>100],
			['record_time, request_url, record_data, sql_command', 'safe'],
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			['audit_id, user_id, record_time, request_ip, request_url, record_data, crud_type, active_record_class, record_id, sql_command, transaction_id, controller_id, action_id, post_params, get_params', 'safe', 'on'=>'search'],
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
			'transaction' => [self::BELONGS_TO, 'TransactionLogs', 'transaction_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'audit_id' => 'Audit',
			'user_id' => 'User',
			'record_time' => 'Record Time',
			'request_ip' => 'Request Ip',
			'request_url' => 'Request Url',
			'record_data' => 'Record Data',
			'crud_type' => 'Crud Type',
			'active_record_class' => 'Active Record Class',
			'record_id' => 'Record',
			'sql_command' => 'Sql Command',
			'transaction_id' => 'Transaction',
			'controller_id' => 'Controller',
			'action_id' => 'Action',
			'post_params' => 'Post Params',
			'get_params' => 'Get Params',
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

		$criteria->compare('audit_id',$this->audit_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('record_time',$this->record_time,true);
		$criteria->compare('request_ip',$this->request_ip,true);
		$criteria->compare('request_url',$this->request_url,true);
		$criteria->compare('record_data',$this->record_data,true);
		$criteria->compare('crud_type',$this->crud_type);
		$criteria->compare('active_record_class',$this->active_record_class,true);
		$criteria->compare('record_id',$this->record_id,true);
		$criteria->compare('sql_command',$this->sql_command,true);
		$criteria->compare('transaction_id',$this->transaction_id,true);
		$criteria->compare('controller_id',$this->controller_id,true);
		$criteria->compare('action_id',$this->action_id,true);
		$criteria->compare('post_params',$this->post_params,true);
		$criteria->compare('get_params',$this->get_params,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}
}