<?php

/**
 * This is the model class for table "configuration".
 *
 * The followings are the available columns in table 'configuration':
 *
 * @property integer $id
 * @property string  $organisationName
 * @property string  $appName
 * @property string  $uploadFolderPath
 * @property string  $imagesFolderPath
 * @property double  $registrationFee
 * @property string  $registrationFeeDescriptionEnglish
 * @property string  $registrationFeeDescriptionDhivehi
 * @property string  $mhcIDPrefix
 * @property integer $mhcIDDigitCount
 * @property integer $mhcIDSuffixYear
 * @property string  $mhcIDSeparator
 * @property integer $noMahramAge
 * @property string  $mahramAgeCompareDate
 * @property integer $yearsInAdvance
 * @property integer $sendUserCodeToEmail
 * @property integer $mobileLoginVerification
 * @property integer $userCodeLength
 * @property integer $userCodeValidityPeriod
 * @property integer $allowRememberUser
 * @property integer $rememberPeriod
 * @property integer $passwordUppercaseRequired
 * @property integer $passwordLowercaseRequired
 * @property integer $passwordNumberRequired
 * @property integer $passwordSymbolRequired
 * @property integer $passwordAllowSpaces
 * @property integer $passwordAllowNumbers
 * @property integer $passwordAllowSymbols
 * @property integer $passwordMinLength
 * @property integer $passwordMaxLength
 * @property integer $receiptCopies
 * @property integer $keepAlerts
 * @property integer $alertPeriod
 * @property integer $devUserId
 * @property integer $pageSize
 * @property string  $sdsNewRegistrationEmail
 * @property string  $sdsStatementGeneratedInfoEmail
 */
class Configuration extends CActiveRecord {

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'configuration';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
        ['appName, registrationFeeDescriptionEnglish, registrationFeeDescriptionDhivehi, organisationName', 'required'],
        ['mhcIDDigitCount, mhcIDSuffixYear, sendUserCodeToEmail, mobileLoginVerification, userCodeLength, userCodeValidityPeriod, allowRememberUser, rememberPeriod, passwordUppercaseRequired, passwordLowercaseRequired, passwordNumberRequired, passwordSymbolRequired, passwordAllowSpaces, passwordAllowNumbers, passwordAllowSymbols, passwordMinLength, passwordMaxLength, receiptCopies, noMahramAge, keepAlerts, alertPeriod, yearsInAdvance, pageSize', 'numerical', 'integerOnly' => true],
        ['registrationFee', 'numerical', 'min' => 0],
        ['appName, registrationFeeDescriptionEnglish, registrationFeeDescriptionDhivehi, organisationName', 'length', 'max' => 255],
        ['mhcIDPrefix', 'length', 'max' => 10],
        ['mhcIDDigitCount', 'numerical', 'min' => 4, 'max' => 10],
        ['alertPeriod', 'numerical', 'min' => 15, 'max' => 240],
        ['mhcIDSeparator', 'length', 'max' => 1],
        ['mahramAgeCompareDate', 'safe'],
        ['userCodeLength', 'numerical', 'max' => 10, 'min' => 5],
        ['pageSize', 'numerical', 'max' => 500, 'min' => 10],
        ['passwordMinLength', 'numerical', 'min' => 5],
        ['passwordMaxLength', 'numerical', 'max' => 16],
        ['noMahramAge', 'numerical', 'max' => 80],
        ['userCodeValidityPeriod', 'numerical', 'min' => 5, 'max' => 240],
        ['sdsNewRegistrationEmail, sdsStatementGeneratedInfoEmail', 'email',
         'on' => 'sdsEmailVerificationOnly'],
        // The following rule is used by search().
        // @todo Please remove those attributes that should not be searched.
        ['id, appName, registrationFee, registrationFeeDescriptionEnglish, registrationFeeDescriptionDhivehi, mhcIDPrefix, mhcIDDigitCount, mhcIDSuffixYear, mhcIDSeparator, organisationName, sendUserCodeToEmail, mobileLoginVerification, userCodeLength, userCodeValidityPeriod, allowRememberUser, rememberPeriod, passwordUppercaseRequired, passwordLowercaseRequired, passwordNumberRequired, passwordSymbolRequired, passwordAllowSpaces, passwordAllowNumbers, passwordAllowSymbols, passwordMinLength, passwordMaxLength, receiptCopies, noMahramAge, mahramAgeCompareDate, keepAlerts, alertPeriod, yearsInAdvance, pageSize', 'safe', 'on' => 'search'],
    ];
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
    ];
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
        'id' => 'ID',
        'appName' => 'Application Name',
        'registrationFee' => 'Min. Amount',
        'registrationFeeDescriptionEnglish' => 'Description',
        'registrationFeeDescriptionDhivehi' => 'ނަން',
        'mhcIDPrefix' => 'Prefix',
        'mhcIDDigitCount' => 'Digit Count',
        'mhcIDSuffixYear' => 'Suffix membership year',
        'mhcIDSeparator' => 'Separator character',
        'organisationName' => 'Full Organization Name',
        'sendUserCodeToEmail' => 'Send to Email',
        'mobileLoginVerification' => 'Use Code for each login',
        'userCodeLength' => 'Code Length',
        'userCodeValidityPeriod' => 'Validity minutes',
        'allowRememberUser' => 'Remember Users',
        'rememberPeriod' => 'Remember hours',
        'passwordUppercaseRequired' => 'Uppercase Required',
        'passwordLowercaseRequired' => 'Lowercase Required',
        'passwordNumberRequired' => 'Number Required',
        'passwordSymbolRequired' => 'Symbol Required',
        'passwordAllowSpaces' => 'Allow Spaces',
        'passwordAllowNumbers' => 'Allow Numbers',
        'passwordAllowSymbols' => 'Allow Symbols',
        'passwordMinLength' => 'Minimum Length',
        'passwordMaxLength' => 'Maximum Length',
        'receiptCopies' => 'Receipt Copies',
        'noMahramAge' => 'If age above (years)',
        'mahramAgeCompareDate' => 'Age cut-off date',
        'keepAlerts' => 'Do not remove alerts',
        'alertPeriod' => 'Alert stay seconds',
        'yearsInAdvance' => 'Years in advance',
        'pageSize' => 'Rows per data table page',
        'sdsNewRegistrationEmail' => 'Member Verfication Email',
        'sdsStatementGeneratedInfoEmail' => 'Monthly Statement Email',
    ];
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
    $criteria->compare('appName', $this->appName, true);
    $criteria->compare('registrationFee', $this->registrationFee);
    $criteria->compare('registrationFeeDescriptionEnglish', $this->registrationFeeDescriptionEnglish, true);
    $criteria->compare('registrationFeeDescriptionDhivehi', $this->registrationFeeDescriptionDhivehi, true);
    $criteria->compare('mhcIDPrefix', $this->mhcIDPrefix, true);
    $criteria->compare('mhcIDDigitCount', $this->mhcIDDigitCount);
    $criteria->compare('mhcIDSuffixYear', $this->mhcIDSuffixYear);
    $criteria->compare('mhcIDSeparator', $this->mhcIDSeparator, true);
    $criteria->compare('organisationName', $this->organisationName, true);
    $criteria->compare('sendUserCodeToEmail', $this->sendUserCodeToEmail);
    $criteria->compare('mobileLoginVerification', $this->mobileLoginVerification);
    $criteria->compare('userCodeLength', $this->userCodeLength);
    $criteria->compare('userCodeValidityPeriod', $this->userCodeValidityPeriod);
    $criteria->compare('allowRememberUser', $this->allowRememberUser);
    $criteria->compare('rememberPeriod', $this->rememberPeriod);
    $criteria->compare('passwordUppercaseRequired', $this->passwordUppercaseRequired);
    $criteria->compare('passwordLowercaseRequired', $this->passwordLowercaseRequired);
    $criteria->compare('passwordNumberRequired', $this->passwordNumberRequired);
    $criteria->compare('passwordSymbolRequired', $this->passwordSymbolRequired);
    $criteria->compare('passwordAllowSpaces', $this->passwordAllowSpaces);
    $criteria->compare('passwordAllowNumbers', $this->passwordAllowNumbers);
    $criteria->compare('passwordAllowSymbols', $this->passwordAllowSymbols);
    $criteria->compare('passwordMinLength', $this->passwordMinLength);
    $criteria->compare('passwordMaxLength', $this->passwordMaxLength);
    $criteria->compare('receiptCopies', $this->receiptCopies);
    $criteria->compare('noMahramAge', $this->noMahramAge);
    $criteria->compare('mahramAgeCompareDate', $this->mahramAgeCompareDate, true);
    $criteria->compare('keepAlerts', $this->keepAlerts);
    $criteria->compare('alertPeriod', $this->alertPeriod);
    $criteria->compare('yearsInAdvance', $this->yearsInAdvance);
    $criteria->compare('pageSize', $this->pageSize);

    return new CActiveDataProvider($this, [
        'criteria' => $criteria,
    ]);
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Configuration the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public function behaviors() {
    return [
        'ActiveRecordDateBehavior' =>
        'application.behaviors.ActiveRecordDateBehavior',
    ];
  }

}
