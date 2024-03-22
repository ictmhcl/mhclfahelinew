<?php

class password extends CValidator {

  public $uppercaseRequired = false;
  public $lowercaseRequired = false;
  public $numberRequired = false;
  public $symbolRequired = false;
  public $minLength = null;
  public $maxLength = null;
  public $allowSpaces = true;
  public $allowNumbers = true;
  public $allowSymbols = true;

  private function passwordMatch($password) {

    $hasUpper = preg_match('@[A-Z]@', $password); // Uppercase alphabet
    $hasLower = preg_match('@[a-z]@', $password); // lowercase alphabet
    $hasNumeral = preg_match('@[0-9]@', $password); // numeric
    $hasSymbol = preg_match('@[^a-zA-Z0-9 ]@', $password); //non alphanumeric but not space
    $hasSpace = preg_match('@[ ]@', $password); // no spaces

    $this->allowNumbers = $this->numberRequired || $this->allowNumbers;
    $this->allowSymbols = $this->symbolRequired || $this->allowSymbols;


    if ($this->uppercaseRequired && !$hasUpper)
      return false;

    if ($this->lowercaseRequired && !$hasLower)
      return false;

    if ($this->numberRequired && !$hasNumeral)
      return false;

    if ($this->symbolRequired && !$hasSymbol)
      return false;

    if (!is_null($this->minLength) && strlen($password) < $this->minLength)
      return false;

    if (!is_null($this->maxLength) && strlen($password) > $this->maxLength)
      return false;

    if (!$this->allowSpaces && $hasSpace)
      return false;

    if (!$this->allowNumbers && $hasNumeral)
      return false;

    if (!$this->allowSymbols && $hasSymbol)
      return false;


    return true;

  }

  private function messageText($object, $attribute) {

    if (is_a($object, 'CMOdel')) {
      $attributeLabel = $object->getAttributeLabel($attribute);
    } else {
      $attributeLabel = 'Password';
    }

    $required = [];
    $requiredMessage = '';
    if ($this->uppercaseRequired)
      $required[] = 'an uppercase character';
    if ($this->lowercaseRequired)
      $required[] = 'a lowercase character';
    if ($this->numberRequired)
      $required[] = 'a numeric digit';
    if ($this->symbolRequired)
      $required[] = 'a non-alphanumeric character (excluding space)';
    if (!is_null($this->minLength))
      $required[] = 'minimum ' . (int)$this->minLength . ' characters';
    if (!is_null($this->maxLength))
      $required[] = 'maximum ' . (int)$this->maxLength . ' characters';

    $requiredCount = count($required);
    if ($requiredCount > 0) {

      $requiredMessage = $attributeLabel . ' must have';

      for ($i = 1; $i <= $requiredCount; $i++) {
        if ($i == 1)
          $requiredMessage .= ' ' . $required[$i - 1];
        elseif ($i < $requiredCount)
          $requiredMessage .= ', ' . $required[$i - 1];
        else
          $requiredMessage .= ' and ' . $required[$i - 1];
      }

      $requiredMessage .= '.';

    }

    $notAllowed = [];
    if (!$this->allowSpaces)
      $notAllowed[] = 'spaces';
    if (!$this->allowNumbers)
      $notAllowed[] = 'numeric digits';
    if (!$this->allowSymbols)
      $notAllowed[] = 'non-alphanumeric characters';

    $notAllowedCount = count($notAllowed);
    $notAllowedMessage = '';
    if ($notAllowedCount > 0) {

      $notAllowedMessage = $attributeLabel . ' cannot have';

      for ($i = 1; $i <= $notAllowedCount; $i++) {
        if ($i == 1)
          $notAllowedMessage .= ' ' . $notAllowed[$i - 1];
        elseif ($i < $notAllowedCount)
          $notAllowedMessage .= ', ' . $notAllowed[$i - 1];
        else
          $notAllowedMessage .= ' or ' . $notAllowed[$i - 1];
      }
      $notAllowedMessage .= '.';
    }

    return trim($requiredMessage . ' ' . $notAllowedMessage);


  }


  protected function validateAttribute($object, $attribute) {

// extract the attribute value from it's model object

    $value = $object->$attribute;
    if (!$this->passwordMatch($value))
      $this->addError($object, $attribute, $this->messageText($object, $attribute));
    elseif (!empty(ZCommonPasswords::model()->findByPk($value)))
      $this->addError($object, $attribute, 'Selected Password is found on common password database');
    else {
      if (is_a($object, 'UserUpdate')) {
        /** @var UserUpdate $object */
        /** @var Users $user */
        $user = Users::model()->findByAttributes(['user_name'=>$object->user_name]);
        $name = str_replace(['  ', '   ', '    '], ' ',
          $user->person->full_name_english);
        $nameParts = explode(' ',$name);
        $nameParts[] = $user->user_name;
        $nameParts[] = $user->person->phone;
        foreach($nameParts as $namePart) {
          if (!empty($namePart))
            if (strstr($object->$attribute, $namePart))
            $this->addError($object, $attribute, 'Password cannot contain
            your user name, any of your names or your phone number');
        }
      }
    }

  }
}

?>