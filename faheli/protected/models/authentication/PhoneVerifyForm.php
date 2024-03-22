<?php

/**
 * PhoneVerifyForm class.
 * PhoneVerifyForm is the data structure for keeping
 * member Phone Verification form data. It is used by the 'verify' action of
 * 'SiteController'.
 */
class PhoneVerifyForm extends CFormModel {

  public $idCard;
  public $mobile;
  public $country_id = Constants::MALDIVES_COUNTRY_ID;
  
  public $person;

  /**
   * Declares the validation rules.
   * The rules state that username and password are required,
   * and password needs to be authenticated.
   */
  public function rules() {
    return [
      ['idCard, mobile', 'required', 'message' => '{attribute}' . H::t('site','required')],
      ['idCard', 'ext.validators.idCard'],
      ['mobile', 'ext.validators.mobilePhoneNumber'],
      ['mobile', 'isRegistered'],
    ];
  }

  /**
   * Declares attribute labels.
   */
  public function attributeLabels() {
    return [
      'idCard' => H::t('site', 'id_no'),
      'mobile' => H::t('site', 'mobile'),
    ];
  }

  /**
   * Checks if the id card & phone number is valid
   */
  public function isRegistered($attribute, $params) {
    if (!empty($this->errors))
      return;
    $personCriteria = new CDbCriteria();
    $personCriteria->with = ['member', 'umraPilgrims'];
    $personCriteria->join = 'left join online_registration_form orf
        on orf.id_no = t.id_no left join users u on u.person_id = t.id';
    $personCriteria->compare('t.id_no', $this->idCard);
    $personCriteria->compare('member.phone_number_1', $this->mobile,true,'AND',true);
    $umraCriteria = new CDbCriteria();
    $umraCriteria->compare('t.id_no', $this->idCard);
    $umraCriteria->compare('umraPilgrims.phone_number', $this->mobile,true,'AND',true);
    $personCriteria->mergeWith($umraCriteria,'OR');
    $onlineUserCriteria = new CDbCriteria();
    $onlineUserCriteria->compare('t.id_no', $this->idCard);
    $onlineUserCriteria->compare('orf.approved', 1);
    $onlineUserCriteria->compare('orf.phone_number_1', $this->mobile,true,'AND', true);
    $personCriteria->mergeWith($onlineUserCriteria, 'OR');
    $adminUserCriteria = new CDbCriteria();
    $adminUserCriteria->compare('t.id_no', $this->idCard);
    $adminUserCriteria->compare('u.mobile_number', $this->mobile,true,'AND',true);
    $personCriteria->mergeWith($adminUserCriteria, 'OR');
    $this->person = Persons::model()->find($personCriteria);
    if (empty($this->person))
      $this->addError('mobile', H::t('site','noMatch'));
  }


  public function sendVerificationCode() {
    if ($this->validate()) {
      $loginCode = PersonLogin::generateLoginCode($this->person, $this->mobile);
      if ($loginCode !== false)
      {
        Helpers::textMessage($this->mobile,
          H::t('site','faheliLoginCodeIs') . $loginCode);
        return true;
      }
      return false;
    }
  }

}
