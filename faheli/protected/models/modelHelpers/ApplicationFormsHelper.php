<?php

/*
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from ... in writing.
 */

/**
 * Description of ApplicationFormsHelper
 *
 * @author nazim
 */
class ApplicationFormsHelper extends ApplicationForms {

  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public function registerMember($onlineMemberMhcNo = null) {

    // Person Details
    $applicantPerson = new Persons();
    $applicantPerson->id_no = $this->id_no;
    $applicantPerson->full_name_english = $this->applicant_full_name_english;
    $applicantPerson->full_name_dhivehi = $this->applicant_full_name_dhivehi;
    $applicantPerson->gender_id = $this->applicant_gender_id;
    $applicantPerson->d_o_b = $this->d_o_b;

    // Permanent Address
    $applicantPerson->perm_address_island_id = $this->perm_address_island_id;
    $applicantPerson->perm_address_english = $this->perm_address_english;
    $applicantPerson->perm_address_dhivehi = $this->perm_address_dhivehi;
    $applicantPerson->country_id = $this->country_id;

    $member = new Members();

    // Contact Info
    $member->phone_number_1 = $this->phone_number_1;
    $member->email_address = $this->email_address;

    // Family No & Emergency Contact
    $member->family_contact_no = $this->family_contact_no;
    $member->emergency_contact_full_name_english = $this->emergency_contact_full_name_english;
    $member->emergency_contact_phone_no = $this->emergency_contact_phone_no;

    // hajj details and group
    $member->preferred_year_for_hajj = $this->preferred_year_for_hajj;
    $member->badhal_hajj = $this->badhal_hajj;
    $member->group_name = $this->group_name;


    // generate a MHC number
    $member->mhc_no = Members::generateMemberNumber();

    $dbTransaction = Yii::app()->db->beginTransaction();

    try {
      // update application information (state id)
      $this->state_id = Constants::APPLICATION_PAYMENT_PENDING;
      if (!$this->save()) {
        Helpers::warning($this->errors);
        return false;
      }

      // find if ID Card exists of the person being saved, replace the information
      /** @var Persons $existingPerson */
      $existingPerson = Persons::model()->findByAttributes(['id_no' => $applicantPerson->id_no]);

      if (empty($existingPerson)) {
        if (!empty($applicantPerson->id_no) && !$applicantPerson->save(false))
          return false;
      } else {

        // replace provided fields & do not change old values of new empty fields
        $applicantPerson->id = $existingPerson->id;
        foreach ($applicantPerson->attributeNames() as $attribute) {
          if (!empty($applicantPerson->$attribute))
            $existingPerson->$attribute = $applicantPerson->$attribute;
        }

        if (!$existingPerson->save())
          return false;
      }

      // save compulsory information
      $member->person_id = $applicantPerson->id;

      // member internals
      $member->application_form_id = $this->id;
      $member->membership_date = date('Y-m-d');
      $member->state_id = Constants::MEMBER_PENDING_FIRST_PAYMENT;

      // save member details
      if (!$member->save())
        return false;


      // if (empty($onlineMemberMhcNo)) {
        // $mhcNo = MhcPoll::pollNewMemberID(MhcPoll::PRODUCT_HAJJ);
        // if (!$mhcNo)
          // throw new CException('Could not get a valid Mhc Number.');
      // } else {
        // $mhcNo = $onlineMemberMhcNo;
      // }
      // $member->mhc_no = $mhcNo;
      if (!$member->save())
        return false;

      $dbTransaction->commit();
      //MhcPoll::updateMember($member->id);


      return $member->id;
    } catch (Exception $ex) {
      if (Yii::app() instanceof CWebApplication)
        Yii::app()->user->setFlash('error', $ex->getMessage());
      $dbTransaction->rollback();
      return false;
    }
  }

  public function getMahramRequired() {

    if ($this->applicant_gender_id != Constants::GENDER_FEMALE)
      return false;

    $cmpDate = Helpers::config('mahramAgeCompareDate');
    if (Helpers::age($this->d_o_b, $cmpDate) < Helpers::config('noMahramAge'))
      return true;

    if ((trim($this->previous_hajj_year)) != '')
      return true;

    return false;
  }

  public function validateBadhalHajj($attribute, $params) {
    if (($this->badhal_hajj || !empty($this->badhal_hajj_person_full_name_english) || !empty($this->badhal_hajj_person_full_name_dhivehi))
      && empty($this->$attribute)
    ) {
      $this->addError($attribute, $this->getAttributeLabel($attribute) . ' is required if any information about Badhal Hajj is provided!');
    }
  }

}
