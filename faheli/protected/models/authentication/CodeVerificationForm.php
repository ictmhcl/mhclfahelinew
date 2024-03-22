<?php

/**
 * CodeVerificationForm class.
 * CodeVerificationForm is the data structure for keeping
 * member Phone Verification form data. It is used by the 'verify' action of
 * 'SiteController'.
 */
class CodeVerificationForm extends CFormModel {

  public $id;
  public $code;
  private $_identity;

  public $person;

  /**
   * Declares the validation rules.
   * The rules state that username and password are required,
   * and password needs to be authenticated.
   */
  public function rules() {
    return [
      ['id, code', 'required', 'message' => '{attribute} ' . H::t('site',
          'required')],
      ['id', 'validCode'],
    ];
  }

  /**
   * Declares attribute labels.
   */
  public function attributeLabels() {
    return [
      'id' => '',
      'code' => H::t('site', 'code'),
    ];
  }

  /**
   * Checks if the code is valid
   */
  public function validCode($attribute, $params) {
    $this->person = PersonLogin::loginPerson($this->id, $this->code);
    if ($this->person === false)
      $this->addError('code', H::t('site','codeIncorrect'));
  }


  public function login() {
    if ($this->_identity === null) {
      $this->_identity = new UserIdentity($this->id, $this->code);
      $this->_identity->authenticate();
    }
    if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
      $duration = (3600 *
        ((int)Helpers::config('rememberPeriod')));
      Yii::app()->user->login($this->_identity, $duration);

      return true;
    } else {
      return false;
    }
  }

}
