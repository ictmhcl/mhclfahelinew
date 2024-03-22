<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuditOpLog
 *
 * @author Dev-Nazim
 */
class AuditOpLog extends CApplicationComponent {
  //Audit record tables

  const RAW_AUDIT_TABLE = "audits";
  const RAW_TRANSACTION_TABLE = "transaction_logs";
  const XML_OBJECTS_TABLE = "audit_log";

  //Oeration log record tables
  const OPERATION_LOG_TABLE = "operation_logs";
  const OPERATION_LOG_TYPES_TABLE = "operation_log_types";
  
  //Yii Session table
  const YII_SESSION_TABLE = "yiisession";
  
  //Default value for operation_log_id column when creating the column.
  const DEFAULT_OPERATION_LOG_ID = '1';
  
  //Operation Types
  const OP_TYPE_INITIAL_RECORD = 1;
  const OP_TYPE_NEW = 2;
  const OP_TYPE_EDIT = 3;
  const OP_TYPE_MARK_DELETE = 4;

  public $rawLog = false;
  public $xmlLog = false;
  public $xmlAuditLog;
  public $collectChildren = true;
  public $opLog = false;
  public $auditTables = [
      self::RAW_AUDIT_TABLE, self::RAW_TRANSACTION_TABLE, self::XML_OBJECTS_TABLE,
      self::OPERATION_LOG_TABLE, self::OPERATION_LOG_TYPES_TABLE,
      //Also ignore Yii Session Table
      self::YII_SESSION_TABLE
  ];

  public function operationLog(CActiveRecord &$model) {

    $conn = Yii::app()->db;
    
    //Eliminate operation log for tables not in main db connection or belongs to Yii framework
    if ($model->dbConnection<>$conn || substr($model->tableName(), 0, 3)=='yii')
      return;

    $opLogModel = new OperationLogs();
    
    $currentUser = Persons::model()->findByPk(Yii::app()->user->id);
    
    $tableName = $model->tableName();
    
    $modelColSchema = [];

    
    //check if model table has 'operation_log_id' in its columns, if not create the column
    if (is_object($conn->schema->getTable($tableName))) {
      $modelColSchema = $conn->schema->getTable($tableName)->getColumnNames();
    }

    if (!in_array('operation_log_id',$modelColSchema)) {
      $this->createOperationLogColumn($model, $opLogModel);
    }
    
    //if this is a new Record
    if ($model->isNewRecord) {
      $this->newOpLogRecord($opLogModel, $currentUser);
    }
    else 
    {
        
      $id = $model->operation_log_id;

      //If the original operation_log_id was created by System (this class)
      if ($model->operation_log_id==(int)self::DEFAULT_OPERATION_LOG_ID) {
        
        //echo 'have to duplicate initial record';
        
        $defaultOpLogRecord = OperationLogs::model()->findByPk((int) self::DEFAULT_OPERATION_LOG_ID);
        
        $newOpLogRecord = new OperationLogs();
        $newOpLogRecord->operation_type_id = $defaultOpLogRecord->operation_type_id;
        $newOpLogRecord->created_by_user_id = $defaultOpLogRecord->created_by_user_id;
        $newOpLogRecord->created_ip = $defaultOpLogRecord->created_ip;
        $newOpLogRecord->created_time = $defaultOpLogRecord->created_time;
        $newOpLogRecord->remarks = 'System-initialized record';
        
        //echo '<br>record initialised... Trying to save now...';
        
        try {
          
          $newOpLogRecord->save();
          //echo '<br>Oplog recrd saved<br>';
          
        } catch (CDbException $e) {
          Yii::app()->user->setFlash('error','There has been an internal error. Please try later!');
          Yii::app()->controller->redirect(['/']);
        }
        
        $id = $newOpLogRecord->operation_log_id;
        $model->operation_log_id = $id;
        //echo 'new id = '.$id.'<br>';
        
      }
      
      $opLogModel = OperationLogs::model()->findByPk($id);
      
      if ($opLogModel===null) {
        Yii::app()->user->setFlash('error','There has been an internal error. Please try later!');
        Yii::app()->controller->redirect(['/']);
      }
      
      $this->updateOpLogRecord($opLogModel, $currentUser);
      
    }

    try {
      $opLogModel->save(false);
      
      $model->operation_log_id = $opLogModel->operation_log_id;

      //CVarDumper::dump($model->attributes,10,1);
      
      return $opLogModel->operation_log_id;

    } catch (CDbException $e) {
      Yii::app()->user->setFlash('error','There has been an internal error. Please try later!');
      Yii::app()->controller->redirect(['/']);
    }
    
    
  }
  
  private function updateOpLogRecord(CActiveRecord &$opLogModel, CActiveRecord $user) {
    $opLogModel->operation_type_id = self::OP_TYPE_EDIT;
    $opLogModel->modified_by_user_id = $user->id;
    $opLogModel->modified_time = date("Y-m-d H:i:s");
    $opLogModel->modified_ip = Yii::app()->request->userHostAddress;
  }

  private function newOpLogRecord(&$opLogModel, $user) {

    $opLogModel->operation_type_id = self::OP_TYPE_NEW;
    $opLogModel->created_by_user_id = 
            isset($user) ? $user->id : Null;
    $opLogModel->created_time = date("Y-m-d H:i:s");
    $opLogModel->created_ip = Yii::app()->request->userHostAddress;
  }

  private function createOperationLogColumn($model, $opLogModel) {

    $conn = Yii::app()->db;
    $tableName = $model->tableName();

    //sql generation using CDbSchema
    $opLogColumnCreateSql = $conn->schema->addColumn(
            $tableName, 'operation_log_id', 
            'int(11) NOT NULL DEFAULT ' . self::DEFAULT_OPERATION_LOG_ID );
    $opLogColumnFKSql = $conn->schema->addForeignKey(
            'fk_' . $tableName . '_operation_log_id', $tableName, 
            'operation_log_id', 'operation_logs', 'operation_log_id');

    try {
      //execute column creation sql
      $conn->createCommand($opLogColumnCreateSql)->execute();
      //execute foreign key relation sql only if operation log table is in same database
      if ($model->dbConnection == $opLogModel->dbConnection) {
        $conn->createCommand($opLogColumnFKSql)->execute();
      }
//      echo 'column created!';
      $meta = Yii::app()->db->schema->refresh();
      $model->refreshMetaData();
      $model->operation_log_id = self::DEFAULT_OPERATION_LOG_ID;

    } catch (CDbException $e) {

      //echo $e->getMessage();exit;
      Yii::app()->user->setFlash('error', 'There has been an internal error. Please try later!');
      Yii::app()->controller->redirect(['/']);

    }
  }

}

?>
