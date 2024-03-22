<?php

/**
 * This is the model class for table "application_forms".
 *
 * The followings are the available columns in table 'application_forms':
 * @property integer                        $id
 * @property integer                        $state_id
 * @property string                         $application_date
 * @property string                         $id_no
 * @property string                         $applicant_full_name_english
 * @property string                         $applicant_full_name_dhivehi
 * @property string                         $d_o_b
 * @property integer                        $applicant_gender_id
 * @property integer                        $perm_address_island_id
 * @property string                         $perm_address_english
 * @property string                         $perm_address_dhivehi
 * @property string                         $country_id
 * @property string                         $phone_number_1
 * @property string                         $email_address
 * @property string                         $family_contact_no
 * @property string                         $emergency_contact_full_name_english
 * @property string                         $emergency_contact_full_name_dhivehi
 * @property string                         $emergency_contact_phone_no
 * @property integer                        $preferred_year_for_hajj
 * @property boolean                        $badhal_hajj
 * @property string                         $group_name
 * @property string                         $id_copy
 * @property string                         $passport_photo
 * @property string                         $mahram_document
 * @property string                         $medical_document
 * @property integer                        $operation_log_id
 *
 * The followings are the available model relations:
 * @property ApplicationFormVerifications[] $applicationFormVerifications
 * @property ApplicationFormVerifications   $applicationFormVerification
 * @property ZGender                        $applicantGender
 * @property ZIslands                       $permAddressIsland
 * @property ZApplicationStates             $state
 * @property Members[]                      $members
 * @property ZCountry                       $permCountry
 * @property OperationLogs                  $operationLog
 */
class ApplicationForms extends CActiveRecord {

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'application_forms';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
      ['application_date, id_no, applicant_full_name_english,
        applicant_full_name_dhivehi, applicant_gender_id,'.
       H::tf('emergency_contact_full_name_dhivehi,').
       'emergency_contact_phone_no,
        perm_address_english, perm_address_dhivehi,
        d_o_b, phone_number_1', 'required', 'message' => '{attribute}' .
        H::t('site','required')],
      ['application_form','required', 'message' => '{attribute}' . H::t('site','attach')],
      ['email_address', 'email'],
      ['perm_address_island_id', 'requiredForMaldives'],
      ['id_no', 'checkID'],
      ['id_no', 'countryUniqueId'],
      ['applicant_full_name_english', 'ext.validators.personalName'],
      ['applicant_full_name_dhivehi', 'dhivehiNameCheck'],
      ['state_id, applicant_gender_id, country_id, perm_address_island_id, 
        operation_log_id, preferred_year_for_hajj, badhal_hajj', 'numerical',
        'integerOnly' => true],
      ['preferred_year_for_hajj', 'numerical', 'min' => 1440, 'max' => 1600,
        'tooSmall' => "{attribute} cannot be before {min}.",
        'tooBig' => "{attribute} cannot be after {max}."
      ],
      ['applicant_full_name_english, applicant_full_name_dhivehi,
        perm_address_english, perm_address_dhivehi, email_address,
        emergency_contact_full_name_english, emergency_contact_full_name_dhivehi,
        id_copy, passport_photo, mahram_document',
        'length', 'max' => 255],
      ['phone_number_1, family_contact_no, emergency_contact_phone_no, ',
        'length', 'max' => 25],
      ['group_name', 'length', 'max' => 100],
    ];
  }

  public function countryUniqueId() {
    /** @var ApplicationForms $existingRecord */
    $existingRecord = self::model()->findByAttributes([
      'id_no' => $this->id_no,
      'country_id' => $this->country_id
    ]);
    if (empty($existingRecord)) return;

    if ($this->isNewRecord || ($this->id != $existingRecord->id))
      $this->addError('id_no', 'ID Number already exists in the system');
  }

  public function dhivehiNameCheck() {
    if (strstr($this->applicant_full_name_dhivehi, '?') !== false)
      $this->addError('applicant_full_name_dhivehi', 'Dhivehi Name needs to
      be fixed.');
  }

  public function requiredForMaldives() {
    if ($this->country_id == Constants::MALDIVES_COUNTRY_ID &&
      empty($this->perm_address_island_id)
    )
      $this->addError('perm_address_island_id', 'Permanent Atoll & Island cannot be '
        . 'blank for Maldivians!');
  }

  public function checkID() {
    if ($this->country_id == Constants::MALDIVES_COUNTRY_ID &&
      !preg_match(Constants::ID_CARD_PATTERN, $this->id_no)
    ) {
      $this->addError('id_no', 'Maldivian ID Card does not match expected pattern!');
    }
  }

  /** Capitalizes English Names */
  public function afterFind() {
    foreach ($this->attributeNames() as $attribute) {
      if (strtolower(substr($attribute, -7)) == 'english')
        $this->$attribute = ucwords(strtolower($this->$attribute));
    }
    parent::afterFind();
  }

  public function behaviors() {
    return [
      // Classname => path to Class
      'ActiveRecordDateBehavior' =>
        'application.behaviors.ActiveRecordDateBehavior',
    ];
  }


  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
      'applicationFormVerifications' => [self::HAS_MANY, 'ApplicationFormVerifications', 'application_form_id'],
      'applicantGender' => [self::BELONGS_TO, 'ZGender', 'applicant_gender_id'],
      'emergencyContactPermAddressIsland' => [self::BELONGS_TO, 'ZIslands', 'emergency_contact_perm_address_island'],
      'permAddressIsland' => [self::BELONGS_TO, 'ZIslands', 'perm_address_island_id'],
      'state' => [self::BELONGS_TO, 'ZApplicationStates', 'state_id'],
      'members' => [self::HAS_MANY, 'Members', 'application_form_id'],
      'applicationFormVerification' => [self::HAS_ONE, 'ApplicationFormVerifications', 'application_form_id'],
      'operationLog' => [self::BELONGS_TO, 'OperationLogs', 'operation_log_id'],
      'permCountry' => [self::BELONGS_TO, 'ZCountry', 'country_id'],

    ];
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
      'id' => 'ID',
      'state_id' => 'State',
      'application_date' => 'Application Date',
      'id_no' => 'ID Card',
      'applicant_full_name_english' => 'Full Name',
      'applicant_full_name_dhivehi' => 'ފުރިހަމަ ނަން',
      'applicant_gender_id' => 'Gender',
      'd_o_b' => 'D.O.B',
      'perm_address_island_id' => 'Island',
      'perm_address_english' => 'House Name, Street',
      'perm_address_dhivehi' => 'ގޭގެ ނަން، މަގު',
      'd_o_b' => 'D.O.B',
      'phone_number_1' => H::t('site', 'mobile'),
      'email_address' => H::t('site', 'email_address'),
      'family_contact_no' => H::t('hajj', 'nokFamilyPhone'),
//      'emergency_contact_full_name_english' => 'ނަމާއި ފޯނު',
      'emergency_contact_full_name_dhivehi' => H::t('hajj',
        'emergencyNamePhone'),
      'emergency_contact_phone_no' => H::t('hajj', 'emergencyNamePhone'),
      'preferred_year_for_hajj' => 'Year',
      'badhal_hajj' => H::t('hajj', 'badhal_hajj'),
      'group_name' => H::t('hajj','group_name'),
      'id_copy' => 'އަައި.ޑީ ކޮޕީ',
      'application_form' => H::t('hajj','application_form'),
      'mahram_document' => H::t('hajj', 'mahram_document'),
      'operation_log_id' => 'Operation Log',
    ];
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your
   * CActiveRecord descendants!
   *
   * @param string $className active record class name.
   *
   * @return ApplicationForms the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

}
