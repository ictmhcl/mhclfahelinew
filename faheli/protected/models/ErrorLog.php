<?php

/**
 * This is the model class for table "error_log".
 *
 * The followings are the available columns in table 'error_log':
 * @property integer $id
 * @property string  $datetime
 * @property integer $code
 * @property string  $type
 * @property string  $url
 * @property string  $message
 * @property string  $file
 * @property integer $line
 * @property string  $trace
 * @property integer $user_id
 */
class ErrorLog extends CActiveRecord {

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'error_log';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('datetime, code, message, user_id', 'required'),
      array('code, line, user_id', 'numerical', 'integerOnly' => true),
      array('type, url, file, trace', 'safe'),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('id, datetime, code, type, url, message, file, line, trace, user_id', 'safe', 'on' => 'search'),
    );
  }

  public static function exceptionLog(CException $exception) {
    $ex['code'] = $exception->getCode();
    $ex['type'] = "Manual Exception Log";
    $ex['message'] = $exception->getMessage();
    $ex['file'] = $exception->getFile();
    $ex['line'] = $exception->getLine();
    $ex['trace'] = $exception->getTraceAsString() . CJSON::encode([$_POST, $_GET]);
    self::log($ex);
  }


  public static function log($error) {
    /** @var Users $user */
    $user = Yii::app()->user->isGuest ? null :
      Users::model()->findByPk(Yii::app()->user->id);

    $errorLog = new ErrorLog();
    $errorLog->datetime = date('y-m-d H:i:s');
    $errorLog->setAttributes($error);
    $errorLog->user_id = Yii::app()->user->isGuest ? 0 : Yii::app()->user->id;
    $errorLog->url = Yii::app()->request->url;
    $saved = $errorLog->save();

    // email error
    $to = Yii::app()->params['devEmail'];
    $subject = "Error Logged on mhclonline";
    $body = "";
    if (!$saved) {
      $body .= "Error Log was NOT saved. \r\n\r\n";
      foreach ($errorLog->attributeNames() as $attributeName) {
        $body .= $errorLog->getAttributeLabel($attributeName) . ': ' .
          $errorLog->$attributeName . "\r\n";
      }
    } else {
      $body = '<html><body>'.Yii::app()->controller->renderPartial
        ('/site/errorLog', ['data' =>$errorLog],true).'</body></html>';

    }
	
    if (!empty($user))
      $body .= "User Contact: " . $user->person->full_name_english . " (" .
        $user->email . ", " . $user->mobile_number . ")\r\n";

    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Additional headers
    //$headers .= 'Cc: sinaan@mhcl.mv' . "\r\n";
    //$headers .= 'Bcc: ' . Yii::app()->params['devEmail'] . "\r\n";
    $headers .= "From: admin@mhclonline.com";

   // mail($to, $subject, $body, $headers);

  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      'user' => array(self::BELONGS_TO, 'users', 'user_id'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
      'id' => 'ID',
      'datetime' => 'Time',
      'code' => 'Code',
      'type' => 'Type',
      'url' => 'Url',
      'message' => 'Message',
      'file' => 'File',
      'line' => 'Line',
      'trace' => 'Trace',
      'user_id' => 'User',
    );
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
  public function search() {
    // @todo Please modify the following code to remove attributes that should not be searched.

    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('datetime', $this->datetime, true);
    $criteria->compare('code', $this->code);
    $criteria->compare('type', $this->type, true);
    $criteria->compare('url', $this->url, true);
    $criteria->compare('message', $this->message, true);
    $criteria->compare('file', $this->file, true);
    $criteria->compare('line', $this->line);
    $criteria->compare('trace', $this->trace, true);
    $criteria->compare('user_id', $this->user_id);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  /**
   * @return CDbConnection the database connection used for this class
   */
  public function getDbConnection() {
    return Yii::app()->db_errorlog;
  }

  public function behaviors() {
    return array(
      // Classname => path to Class
      'ActiveRecordDateBehavior' =>
        'application.behaviors.ActiveRecordDateBehavior',
    );
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your
   * CActiveRecord descendants!
   *
   * @param string $className active record class name.
   *
   * @return ErrorLog the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

}

/*

CREATE TABLE `error_log` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`datetime` DATETIME NOT NULL,
	`code` INT(11) NOT NULL,
	`type` TINYTEXT NULL,
	`url` TINYTEXT NULL,
	`message` TINYTEXT NOT NULL,
	`file` TINYTEXT NULL,
	`line` INT(11) NULL DEFAULT NULL,
	`trace` TEXT NULL,
	`user_id` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;


*/