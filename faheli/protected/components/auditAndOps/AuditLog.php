<?php

/**
 * This is the model class for table "audit_log".
 *
 * The followings are the available columns in table 'audit_log':
 * @property integer $audit_log_id
 * @property integer $audit_log_data_type_id
 * @property integer $audit_log_action_type_id
 * @property integer $data_item_id
 * @property string $data
 * @property string $url
 * @property integer $user_id
 * @property integer $date_time
 * @property integer $ip_address
 * @property integer $remarks
 *
 * @property AuditLogActionTypes[] $actionType
 * @property AuditLogDataTypes[] $dataType
 */
class AuditLog extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return AuditLog the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return CDbConnection database connection
   */
  public function getDbConnection() {
    return Yii::app()->db_audit;
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'audit_log';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
        ['audit_log_id, audit_log_data_type_id, audit_log_action_type_id, data_item_id, user_id, date_time', 'numerical', 'integerOnly' => true],
        ['data, url, remarks, ip_address', 'safe'],
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        ['audit_log_id, audit_log_data_type_id, audit_log_action_type_id, data_item_id, data, url, user_id, date_time, ip_address', 'safe', 'on' => 'search'],
    ];
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
        'actionType' => [self::BELONGS_TO, 'AuditLogActionTypes', 'audit_log_action_type_id'],
        'dataType' => [self::BELONGS_TO, 'AuditLogDataTypes', 'audit_log_data_type_id'],
    ];
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
        'audit_log_id' => 'Audit Log',
        'audit_log_data_type_id' => 'Audit Log Data Type',
        'audit_log_action_type_id' => 'Audit Log Action Type',
        'data_item_id' => 'Data Item',
        'data' => 'Data',
        'url' => 'Url',
        'user_id' => 'User',
        'date_time' => 'Date Time',
        'ip_address' => 'Ip Address',
        'remarks' => 'Remarks',
    ];
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search() {
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.

    $criteria = new CDbCriteria;

    $criteria->compare('audit_log_id', $this->audit_log_id);
    $criteria->compare('audit_log_data_type_id', $this->audit_log_data_type_id);
    $criteria->compare('audit_log_action_type_id', $this->audit_log_action_type_id);
    $criteria->compare('data_item_id', $this->data_item_id);
    $criteria->compare('data', $this->data, true);
    $criteria->compare('url', $this->url, true);
    $criteria->compare('user_id', $this->user_id);
    $criteria->compare('date_time', $this->date_time);
    $criteria->compare('ip_address', $this->ip_address);

    return new CActiveDataProvider($this, [
        'criteria' => $criteria,
    ]);
  }

  public function behaviors() {
    return [
        'ActiveRecordDateBehavior' =>
        'application.behaviors.ActiveRecordDateBehavior',
    ];
  }

}
