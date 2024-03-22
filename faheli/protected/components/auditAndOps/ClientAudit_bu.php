<?php

/**
 * Client Audit Class
 * ==================
 *
 * Logs selected CActiveRecord Instances to audit logs in XML tagged format
 *
 *
 * INSTALLATION
 *
 * Step 1:
 * =======
 * Create the following tables in the audit database [separate database from app] (auditLog.sql)
 *
 * CREATE TABLE `audit_log` (
 *   `audit_log_id` int(10) NOT NULL AUTO_INCREMENT,
 *   `audit_log_data_type_id` int(10) DEFAULT NULL,
 *   `audit_log_action_type_id` int(10) DEFAULT NULL,
 *   `data_item_id` int(10) DEFAULT NULL,
 *   `data` text,
 *   `url` text,
 *   `user_id` int(10) DEFAULT NULL,
 *   `date_time` datetime DEFAULT NULL,
 *   `ip_address` int(10) DEFAULT NULL,
 *   PRIMARY KEY (`audit_log_id`)
 * ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
 *
 * CREATE TABLE `audit_log_action_types` (
 *   `audit_log_action_type_id` int(10) NOT NULL AUTO_INCREMENT,
 *   `action_type_name` varchar(250) NOT NULL,
 *   PRIMARY KEY (`audit_log_action_type_id`)
 * ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;
 *
 * CREATE TABLE `audit_log_data_types` (
 *   `audit_log_data_type_id` int(10) NOT NULL AUTO_INCREMENT,
 *   `data_type_name` varchar(250) NOT NULL,
 *   PRIMARY KEY (`audit_log_data_type_id`)
 * ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
 *
 *
 * Step 2:
 * =======
 * Add a db connection in the main config file for the audit database
 * e.g. if the audit database is named 'appdb_auditlog'
 *
 * >         'db_audit' => array(
 * >            'connectionString' => 'mysql:host=localhost:3306;dbname=appdb_auditlog',
 * >            'emulatePrepare' => true,
 * >            'username' => 'user',
 * >            'password' => 'password',
 * >            'charset' => 'utf8',
 * >            'class' => 'CDbConnection'
 *
 * Step 3:
 * =======
 * Copy the following files to /protected/components/audit folder
 * AuditLog.php             (CActiveRecord class for active_log table)
 * AuditLogActionTypes.php  (CActiveRecord class for audit_log_action_types table)
 * AuditLogDataTypes.php    (CActiveRecord class for audit_log_data_types table)
 * ClientAudit.php          (this ClientAudit class file)
 *
 * Step 4:
 * =======
 * Edit the first three files from Step 3, to direct to the correct db connection.
 *
 * e.g. in AuditLog.php, ensure that db connection is same as Step 2.
 *
 * >	public function getDbConnection()
 * >	{
 * >		return Yii::app()->db_audit;
 * >	}
 *
 * Step 5:
 * =======
 * In the main config file under imports include the following line.
 * 'application.components.audit.*',
 *
 * Step 6:
 * =======
 * populate the tables:
 * audit_log_action_types ('insert','update','state change', etc.)
 * audit_log_data_types ('institute','student','class', etc.)
 * ** read the constants declared in this class
 *
 * Step 7:
 * =======
 * REPLACE the constants [have a look at the declarations below] in *this* file with the items
 * from Step 6.
 *
 *
 * USAGE:
 *
 * Step 1:
 * =======
 * In controller where Db Records are Saved/updated/deleted, initialize an instance of
 * this class (ClientAudit) e.g.
 *
 * > $myAudit = new ClientAudit(ClientAudit::AUDIT_ACTION_CREATE, ClientAudit::AUDIT_ACTION_CLASS);
 *
 * The constructor params declares the audit_log_action_type_id and audit_log_data_type_id. They
 * should be present in the tables audit_log_action_types and audit_log_data_types from above.
 *
 *
 * Note: the following steps 2-4 can be done in any order but step 2 is required before step 6.
 *
 * Step 2:
 * =======
 * Set the main db record to be captured for audit e.g.
 *
 * > $myAudit->setMainItem($model);
 *
 *
 * Step 3:
 * =======
 * Add all related child db record(s) to be captured for audit. This can be a single CActiveRecord
 * object or a non-associative array of CActiveRecord objects e.g.
 *
 * > $myAudit->addChild($model);
 *
 * OR
 *
 * > $myAudit->addChild($models); // non-associative array of CActiveRecord objects
 *
 * NOTE: You don't need to include the Users object specifically. ClientAudit class will take
 * the users object automatically as the last child item with the following attributes:
 * user_id, user_name, full_name, email, last_login_date and  mobile
 * (It is necessary that the Users CActiveRecord model be present and 'users' table be present in
 * the main application database)
 *
 * Step 4:
 * =======
 * Continue step 3 as necessary
 *
 *
 * Step 5:
 * =======
 * Write the audit to database (if a main Item is not set, this method will throw an error):
 * e.g.
 *
 * > $myAudit->write();
 *
 * This will return true on success and false on any failure. After successful write, the
 * ClientAudit object will reset main item to null and clear all child records from memory.
 *
 * NOTE: To use the same instance of the ClientAudit object after the write method you will
 * need to start from Step 2 again before further writes. If a mainItem is not set the write
 * method will throw a CException.
 *
 *
 */

/**
 * Description of ClientAudit
 *
 * @author nazim
 */
class ClientAudit_bu {

  // audit action types
  const AUDIT_ACTION_CREATE = 1;
  const AUDIT_ACTION_EDIT = 2;
  const AUDIT_ACTION_DELETE = 3;
  //
  // audit data types
  const AUDIT_DATA_APPLICATION_FORM = 1;
  const AUDIT_DATA_APPLICATION_VERIFICATION = 2;
  const AUDIT_DATA_PAYMENT_COLLECTION = 3;
  const AUDIT_DATA_MEMBER = 4;
  const AUDIT_TABLE = "audit_logs";

  private $_actionType;
  private $_dataType;
  private $_mainItem = null;
  private $_childItems = [];

  /**
   * Constructor for this Audit Object
   * sets actionType and dataType for Audit Object
   * Optionally with mainItem and childItems
   *
   * @param type $actionType
   * @param type $dataType
   * @param type $mainItem
   * @param type $childItems
   */
  public function __construct($actionType, $dataType, $mainItem = null, $childItems = []) {
    //check if transaction has assigned audit objects
    $this->_actionType = $actionType;
    $this->_dataType = $dataType;

    if (!is_null($mainItem))
      $this->setMainItem($mainItem);

    if (!is_null($childItems)) {
      $this->addChild($childItems);
    }
  }

  /**
   * Sets the main item (active record) of the Audit Object
   *
   * @param CActiveRecord $mainItem
   * @throws CException
   */
  public function setMainItem($mainItem) {
    if (is_object($mainItem) && $mainItem instanceof CActiveRecord) {
      $this->_mainItem = $mainItem;
    } else {
      throw new CException(Yii::t('yii', 'The main item "{item_class}" is not a valid ' .
              'instance of CActiveRecord', ['{item_class}' => get_class($mainItem)]));
    }
  }

  /**
   * Returns the main item in audit collection
   *
   * @return type CActiveRecord
   */
  public function getMainItem() {
    return $this->_mainItem;
  }

  /**
   * Add child items to Audit Object
   *
   * @param CActiveRecord or Array of CActiveRecords $childItem
   * @throws CException
   */
  public function addChild($childItem) {
    if (!is_array($childItem))
      $childItems = [$childItem];
    else
      $childItems = $childItem;

    foreach ($childItems as $item) {
      if (is_object($item) && $item instanceof CActiveRecord) {
        $this->addChildItem($item);
      } else {
        throw new CException(Yii::t('yii', 'The child item "{item_class}" is not a valid ' .
                'instance of CActiveRecord', ['{item_class}' => get_class($item)]));
      }
    }
  }

  private function addChildItem($childItem) {
    $this->_childItems[] = $childItem;
  }

  public function write() {

    if (is_null($this->_mainItem) || !isset($this->_mainItem))
      throw new CException('Main item not set. Cannot write Audit!');

    $auditLogModel = new AuditLog;
    $auditLogModel->audit_log_action_type_id = $this->_actionType;
    $auditLogModel->audit_log_data_type_id = $this->_dataType;
    $auditLogModel->data_item_id = $this->_mainItem->getPrimaryKey();
    $auditLogModel->url = Yii::app()->request->hostInfo . Yii::app()->request->url;
    $auditLogModel->user_id = Yii::app()->user->id;
    $auditLogModel->date_time = date("Y-m-d H:i:s");
    $auditLogModel->ip_address = Yii::app()->request->userHostAddress;
    $auditLogModel->data = $this->dataXML();

    if ($auditLogModel->save(false)) {
      $this->_mainItem = null;
      $this->_childItems = [];
      return true;
    } else
      return false;
  }

  private function XMLAttribs($item) {
    $attribs = [];
    foreach ($item->attributes as $k => $v) {
      $attribs[] = addslashes($k) . '="' . addslashes($v) . '"';
    }
    return implode(' ', array_reverse($attribs));
  }

  private function mainItemXML($mainItem, $part) {
    $dataType = AuditLogDataTypes::model()->findByPk($this->_dataType);

    $mainItemTag = (string) $dataType->data_type_name;
    $mainItemAttribs = $this->XMLAttribs($mainItem);

    if ($part == 'opening')
      return '<' . $mainItemTag . ' ' . $mainItemAttribs . '>';

    if ($part == 'closing')
      return '</' . $mainItemTag . '>';
  }

  private function userItem() {
    $userItem = Users::model()->findByPk(Yii::app()->user->id);
    return '    <Users id="' . $userItem->id . '" ' .
            'user_name="' . addSlashes($userItem->user_name) . '" ' .
            'full_name="' . addSlashes($userItem->person->full_name_english) . '" ' .
            'email="' . addSlashes($userItem->email) . '" ' .
            'last_login_datetime="' . addSlashes($userItem->last_login_datetime) . '" ' .
            'mobile="' . addSlashes($userItem->mobile_number) . '" />' . "\r\n";
  }

  private function childItemXML($childItem) {
    return '    <' . get_class($childItem) . ' ' . $this->XMLAttribs($childItem) . ' />';
  }

  public function dataXML() {
    $ret = $this->mainItemXML($this->_mainItem, 'opening') . "\r\n";

    if ((sizeof($this->_childItems)) > 0) {
      foreach ($this->_childItems as $childItem) {
        $ret.= $this->childItemXML($childItem) . "\r\n";
      }
    }
    $ret.= $this->userItem();
    $ret.= $this->mainItemXML($this->_mainItem, 'closing');

    return $ret;
  }

}

?>
