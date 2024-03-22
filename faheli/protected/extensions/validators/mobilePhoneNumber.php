<?php

class mobilePhoneNumber extends CValidator
{
  private $pattern = Constants::MOBILE_PHONE_NUMBER_PATTERN;
  private $errorMessage = 'incorrectMobileNumber';

  protected function validateAttribute($object,$attribute)
  {

      // extract the attribute value from it's model object
      $value=$object->$attribute;
      if(!preg_match($this->pattern, $value) && !empty($value))
      {
          $this->addError($object,$attribute,H::t('site',$this->errorMessage));
      }
  }

  /**
   * Returns the JavaScript needed for performing client-side validation.
   * @param CModel $object the data object being validated
   * @param string $attribute the name of the attribute to be validated.
   * @return string the client-side validation script.
   * @see CActiveForm::enableClientValidation
   */
  public function clientValidateAttribute($object, $attribute) {

    return "
            if(!value.match({$this->pattern})) {
                messages.push(" . CJSON::encode(H::t('site', $this->errorMessage)) . ");
            }
            ";
  }

}


?>