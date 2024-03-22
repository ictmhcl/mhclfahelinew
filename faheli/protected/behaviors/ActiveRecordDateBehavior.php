<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ActiveRecordDateBehavior
 *
 * @author Dev-Nazim
 */
class ActiveRecordDateBehavior extends CActiveRecordBehavior {

  //put your code here

  public function afterFind($event) {

    foreach ($this->owner->attributeNames() as $attrName) {

      //Change dates to dd mmm yyyy format after reading from db
      if (substr($attrName, -4) == 'date' || substr($attrName, -4) == 'Date' ||
              substr($attrName, -5) == 'd_o_b') {
        if (empty($this->owner->$attrName))
          $this->owner->$attrName = null;
        else {
          if (!$this->owner->$attrName instanceof DateTime)
            $dateObj = new DateTime($this->owner->$attrName);
          else
            $dateObj = $this->owner->$attrName;
          $this->owner->$attrName = $dateObj->format('d M Y');
        }
      }
      //Change dates to dd mmm yyyy hh:mm format reading from db
      if (substr($attrName, -8) == 'datetime' || substr($attrName, -4) == 'time') {
        if (empty($this->owner->$attrName))
          $this->owner->$attrName = null;
        else {
          if (!$this->owner->$attrName instanceof DateTime)
            $dateObj = new DateTime($this->owner->$attrName);
          else
            $dateObj = $this->owner->$attrName;
          $this->owner->$attrName = $dateObj->format('d M Y H:i:s');
        }
      }
    }
  }

  public function afterSave($event) {
    $this->afterfind($event);
  }

  public function beforeSave($event) {

    foreach ($this->owner->attributeNames() as $attrName) {

      //Change dates to yyyy-mm-dd format before writing to database
      if (substr($attrName, -4) == 'date' || substr($attrName, -4) == 'Date' ||
              substr($attrName, -5) == 'd_o_b') {
        if (empty($this->owner->$attrName))
          $this->owner->$attrName = null;
        else {
          if (!$this->owner->$attrName instanceof DateTime)
            $dateObj = new DateTime($this->owner->$attrName);
          else
            $dateObj = $this->owner->$attrName;
          $this->owner->$attrName = $dateObj->format(Constants::DATE_SAVE_FORMAT);
        }
      }
      //Change dates to yyyy-mm-dd format before writing to database
      if (substr($attrName, -8) == 'datetime' || substr($attrName, -4) == 'time') {
        if (empty($this->owner->$attrName))
          $this->owner->$attrName = null;
        else {
          if (!$this->owner->$attrName instanceof DateTime)
            $dateObj = new DateTime($this->owner->$attrName);
          else
            $dateObj = $this->owner->$attrName;
          $this->owner->$attrName = $dateObj->format('Y-m-d H:i:s');
        }
      }
    }
    return true;
  }

}

?>
