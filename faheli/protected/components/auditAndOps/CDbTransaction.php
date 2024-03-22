<?php

/**
 * CDbTransaction class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CDbTransaction represents a DB transaction.
 *
 * It is usually created by calling {@link CDbConnection::beginTransaction}.
 *
 * The following code is a common scenario of using transactions:
 * <pre>
 * $transaction=$connection->beginTransaction();
 * try
 * {
 *    $connection->createCommand($sql1)->execute();
 *    $connection->createCommand($sql2)->execute();
 *    //.... other SQL executions
 *    $transaction->commit();
 * }
 * catch(Exception $e)
 * {
 *    $transaction->rollBack();
 * }
 * </pre>
 *
 * @property CDbConnection $connection The DB connection for this transaction.
 * @property boolean $active Whether this transaction is active.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.db
 * @since 1.0
 */
class CDbTransaction extends CComponent {

  private $_connection = null;
  private $_active;
  private $_transactionLog = null;
  public $logId;
  public $auditActionType;
  public $auditDataType;
  public $auditMainItem;
  public $auditChildItems = [];

  /**
   * Constructor.
   * @param CDbConnection $connection the connection associated with this transaction
   * @see CDbConnection::beginTransaction
   */
  public function __construct(CDbConnection $connection) {
    $this->_connection = $connection;
    $this->_active = true;

    if (isset(Yii::app()->audit) && Yii::app()->audit->rawLog) {
      $this->_transactionLog = new TransactionLogs();
      $this->logId = $this->_transactionLog->transaction_id;
    }
  }

  /**
   * Commits a transaction.
   * @throws CException if the transaction or the DB connection is not active.
   */
  public function commit() {
    if ($this->_active && $this->_connection->getActive()) {
      if (isset(Yii::app()->audit) && Yii::app()->audit->rawLog) {
        $this->_transactionLog->end_time = microtime(true);
        $this->_transactionLog->commit_rollback_status = 'commit';
        $this->_transactionLog->save(false);

        //xml audit logs
        $this->_transactionLog = null;
        $this->logId = null;
      }

      if (isset(Yii::app()->audit) && Yii::app()->audit->xmlLog) {
        Yii::app()->audit->xmlAuditLog->write();
        Yii::app()->audit->xmlAuditLog = null;
        Yii::app()->audit->xmlLog = false;
      }

      Yii::trace('Committing transaction', 'system.db.CDbTransaction');
      $this->_connection->getPdoInstance()->commit();
      $this->_active = false;
    } else
      throw new CDbException(Yii::t('yii', 'CDbTransaction is inactive and ' .
              'cannot perform commit or roll back operations.'));
  }

  public function writeAudit() {
    if ($this->_active && $this->_connection->getActive()) {
      if (isset(Yii::app()->audit) && Yii::app()->audit->rawLog) {
        $this->_transactionLog->end_time = microtime(true);
        $this->_transactionLog->commit_rollback_status = 'commit';
        $this->_transactionLog->save(false);

        //xml audit logs
        $this->_transactionLog = null;
        $this->logId = null;
      }

      if (isset(Yii::app()->audit) && Yii::app()->audit->xmlLog) {
        Yii::app()->audit->xmlAuditLog->write();
        Yii::app()->audit->xmlAuditLog = null;
        Yii::app()->audit->xmlLog = false;
      }

    }
  }

  /**
   * Rolls back a transaction.
   * @throws CException if the transaction or the DB connection is not active.
   */
  public function rollback() {
    if ($this->_active && $this->_connection->getActive()) {
      Yii::trace('Rolling back transaction', 'system.db.CDbTransaction');
      $this->_connection->getPdoInstance()->rollBack();
      $this->_active = false;

      if (isset(Yii::app()->audit) && Yii::app()->audit->rawLog) {
        $this->_transactionLog->end_time = microtime(true);
        $this->_transactionLog->commit_rollback_status = 'rollback';
        $this->_transactionLog->save(false);

        //update audit records (sql_success=0)
        $criteria = New CDbCriteria;
        $criteria->compare('transaction_id', $this->logId);
        $audits = new Audits;
        $audits->updateAll(['sql_success' => 0], $criteria);

        $this->_transactionLog = null;
        $this->logId = null;
      }
      if (isset(Yii::app()->audit) && Yii::app()->audit->xmlLog) {
        Yii::app()->audit->xmlAuditLog = null;
        Yii::app()->audit->xmlLog = false;
      }
    } else
      throw new CDbException(Yii::t('yii', 'CDbTransaction is inactive and ' .
              'cannot perform commit or roll back operations.'));
  }

  /**
   * @return CDbConnection the DB connection for this transaction
   */
  public function getConnection() {
    return $this->_connection;
  }

  /**
   * @return boolean whether this transaction is active
   */
  public function getActive() {
    return $this->_active;
  }

  /**
   * @param boolean $value whether this transaction is active
   */
  protected function setActive($value) {
    $this->_active = $value;
  }

  public function doAudit($actionType, $dataType, CActiveRecord $mainItem, $remarks = null, $children = []) {
    if ($this->_active && $this->_connection->getActive()) {
      Yii::app()->audit->xmlAuditLog = new ClientAudit($actionType, $dataType, $mainItem, $children, $remarks);
      Yii::app()->audit->xmlLog = true;
    } else
      throw new CDbException(Yii::t('yii', 'CDbTransaction is inactive and ' .
              'cannot perform xml-audit operations.'));
  }

  public function addAuditChildren($childItems) {
    if ($this->_active && $this->_connection->getActive()) {
      Yii::app()->audit->xmlAuditLog->addChild($childItems);
    } else
      throw new CDbException(Yii::t('yii', 'CDbTransaction is inactive and ' .
              'cannot perform xml-audit operations.'));
  }

}
