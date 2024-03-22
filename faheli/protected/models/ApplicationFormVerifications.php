<?php

/**
 * This is the model class for table "application_form_verifications".
 *
 * The followings are the available columns in table 'application_form_verifications':
 * @property integer $id
 * @property integer $application_form_id
 * @property string $applicant_comment
 * @property integer $applicant_verified
 * @property integer $operation_log_id
 *
 * The followings are the available model relations:
 * @property ApplicationForms $applicationForm
 * @property OperationLogs $operationLog
 */
class ApplicationFormVerifications extends CActiveRecord {

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'application_form_verifications';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
        ['application_form_id', 'required'],
        ['application_form_id, applicant_verified, operation_log_id', 'numerical', 'integerOnly' =>
          true],
        ['applicant_comment', 'length', 'max' => 255],
    ];
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
        'applicationForm' => [self::BELONGS_TO, 'ApplicationForms', 'application_form_id'],
        'operationLog' => [self::BELONGS_TO, 'OperationLogs', 'operation_log_id'],
    ];
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
        'id' => 'ID',
        'application_form_id' => 'Application Form',
        'applicant_comment' => 'Comment',
        'applicant_verified' => 'Applicant Verified',
        'operation_log_id' => 'Operation Log',
    ];
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return ApplicationFormVerifications the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

}
