<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SettingsForm extends CFormModel {

  public $c001; // appName;
  public $c004; // registrationFee;
  public $c005; // registrationFeeDescriptionEnglish;
  public $c006; // registrationFeeDescriptionDhivehi;
  public $c007; // mhcIDPrefix;
  public $c008; // mhcIDDigitCount;
  public $c009; // mhcIDSuffixYear;
  public $c010; // mhcIDSeparator;
  public $c011; // organisationName;
  public $c012; // sendUserCodeToEmail;
  public $c013; // mobileLoginVerification;
  public $c014; // userCodeLength;
  public $c015; // userCodeValidityPeriod;
  public $c016; // allowRememberUser;
  public $c017; // rememberPeriod;
  public $c018; // passwordUppercaseRequired;
  public $c019; // passwordLowercaseRequired;
  public $c020; // passwordNumberRequired;
  public $c021; // passwordSymbolRequired;
  public $c022; // passwordAllowSpaces;
  public $c023; // passwordAllowNumbers;
  public $c024; // passwordAllowSymbols;
  public $c025; // passwordMinLength;
  public $c026; // passwordMaxLength;
  public $c028; // noMahramAge;
  public $c029; // mahramAgeCompareDate;
  public $c030; // keepAlerts;
  public $c031; // alertPeriod;
  public $c032; // yearsInAdvance;
  public $c034; // pageSize;

  /**
   * Declares the validation rules.
   */

  public function rules() {
    return [
        // name, email, subject and body are required
        ['c001, c004, c005, c006, c011', 'required'],
        ['c004', 'numerical', 'min' => 0],
        ['c008, c009, c012, c013, c014, c015, c016, c017, c018, c019, c020, c021, c022, c023, c024, c025, c026, 028', 'numerical', 'integerOnly' => true],
        ['c014', 'numerical', 'max' => 10, 'min' => 5],
        ['c025', 'numerical', 'min' => 5],
        ['c026', 'numerical', 'max' => 16],
        ['c028', 'numerical', 'max' => 80],
        ['c015', 'numerical', 'min' => 5],
        ['c005, c006, c011', 'length', 'max' => 200],
        ['c007', 'length', 'max' => 10],
        ['c010', 'length', 'max' => 1],
        // email has to be a valid email address
        ['email', 'email'],
        // verifyCode needs to be entered correctly
        ['verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()],
    ];
  }

  /**
   * Declares customized attribute labels.
   * If not declared here, an attribute would have a label that is
   * the same as its name with the first letter in upper case.
   */
  public function attributeLabels() {
    return [
        'verifyCode' => 'Verification Code',
    ];
  }

}
