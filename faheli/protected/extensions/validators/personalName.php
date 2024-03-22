<?php

class personalName extends CValidator
{
    private $pattern = Constants::PERSONAL_NAME;

    protected function validateAttribute($object,$attribute)
    {

        // extract the attribute value from it's model object
        $value=$object->$attribute;
        if(!preg_match($this->pattern, $value))
        {
            $this->addError($object,$attribute,'Name contains invalid characters!');
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
                messages.push(" . CJSON::encode('Name contains invalid characters!') . ");
            }
            ";
    }

}


?>