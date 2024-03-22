<?php

class idCard extends CValidator
{
  public $countryId = Constants::MALDIVES_COUNTRY_ID;
  private $pattern = Constants::ID_CARD_PATTERN;

  protected function validateAttribute($object, $attribute)
  {

    // extract the attribute value from it's model object
    $value = $object->$attribute;
    if (!empty($object->country_id) && $object->country_id==$this->countryId &&
      !preg_match($this->pattern, $value) && !empty($value)) {
      $this->addError($object, $attribute, H::t('site', 'incorrectIdFormat'));
    }
  }

//    /**
//     * Returns the JavaScript needed for performing client-side validation.
//     * @param CModel $object the data object being validated
//     * @param string $attribute the name of the attribute to be validated.
//     * @return string the client-side validation script.
//     * @see CActiveForm::enableClientValidation
//     */
//    public function clientValidateAttribute($object, $attribute)
//    {
//
//        return "
//            if(!value.match({$this->pattern})) {
//                messages.push(" . CJSON::encode('ID Card number format is incorrect!') . ");
//            }
//            ";
//    }

}


?>