<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

  private $_id;
  public $code;

  public $errorMessage;



  public function __construct($id, $code) {
    $this->_id = $id;
    $this->code = $code;
  }

  /**
   * Authenticates a user.
   * The example implementation makes sure if the username and password
   * are both 'demo'.
   * In practical applications, this should be changed to authenticate
   * against some persistent user identity storage (e.g. database).
   * @return boolean whether authentication succeeds.
   */
  public function authenticate() {
    $this->errorCode = self::ERROR_NONE;
    return $this->errorCode;
  }


  public function getId() {
    return $this->_id;
  }

}
