<?php

/**
 * This is the model class for table "persons".
 *
 * The followings are the available columns in table 'persons':
 *
*@property integer        $id
 * @property string         $id_no
 * @property string         $full_name_english
 * @property string         $full_name_dhivehi
 * @property string         $three_names_english
 * @property string         $three_names_arabic
 * @property string         $d_o_b
 * @property integer        $perm_address_island_id
 * @property integer        $gender_id
 * @property string         $perm_address_english
 * @property string         $perm_address_dhivehi
 * @property integer        $agreed_to_terms_of_use
 * @property integer        $dnr_verified
 * @property integer        $country_id
 * @property integer        $photo_file_id
 * @property integer        $operation_log_id
 *
 * Calculated Fields
 * @property string         $idName
 * @property string         $ageNow
 * @property string         $personText
 * @property Passports         $latestPassport
 * @property String         $permAddressText
 * @property string         $permAddressTextDhivehi
 * @property String         $atollIsland
 * @property String         $atollIslandDhivehi
 * @property array          $nameParts
 * @property String         $photoUrl
 * @property OnlineRegistrationForm $onlineRegistrationForm
 *
 * The followings are the available model relations:
 * @property Employees[]    $employees
 * @property Members        $member
 * @property NonMhclMembers $nonMember
 * @property Members[]      $members
 * @property UmraPilgrims   $umraPilgrim
 * @property ZGender        $gender
 * @property ZIslands       $permAddressIsland
 * @property ZCountry       $country
 * @property Users[]        $users
 * @property Users          $user
 * @property string         $phone
 * @property string         $email
 * @property string         $idCopy
 * @property MachineEmployees $machineEmployee
 * @property OrganizationStaff[] $organizationStaff
 * @property PersonLogin $personLogin
 * @property ApplicationForms $hajjApplicationForm
 * @property Ageega $ageegas
 */
class Persons extends CActiveRecord {

  public $age;
  public $country_id = Constants::MALDIVES_COUNTRY_ID;

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'persons';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
      ['id_no, full_name_english, full_name_dhivehi, country_id', 'required'],
      ['id_no', 'countryUniqueId'],
      ['perm_address_english, perm_address_dhivehi, d_o_b, gender_id',
        'required', 'on' => 'newMember'],
      ['d_o_b, gender_id, perm_address_english, perm_address_dhivehi,
        perm_address_island_id', 'required', 'on' => 'display'],
      ['d_o_b, gender_id, perm_address_english, perm_address_dhivehi',
        'required', 'on' => 'membership'],
      ['perm_address_island_id, id_no', 'maldivianValidations'],
      ['perm_address_island_id, gender_id, country_id, photo_file_id,
        operation_log_id', 'numerical', 'integerOnly' => true],
      ['id_no', 'length', 'max' => 30],
      ['full_name_english, three_names_english, three_names_arabic,
        full_name_dhivehi, perm_address_english, perm_address_dhivehi',
        'length', 'max' => 255],
//      ['three_names_english','checkThreeNames'],
//      ['d_o_b', 'safe'],
      ['three_names_english, three_names_arabic, dnr_verified, d_o_b', 'safe'],
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      ['id, id_no, full_name_english, full_name_dhivehi, d_o_b, perm_address_island_id, gender_id, perm_address_english, perm_address_dhivehi, photo_file_id, operation_log_id', 'safe', 'on' => 'search'],
    ];
  }

  public function countryUniqueId() {
    $existingRecord = Persons::model()->findByAttributes([
      'id_no' => $this->id_no,
      'country_id' => Constants::MALDIVES_COUNTRY_ID
    ]);
    if (empty($existingRecord)) return;

    if ($this->isNewRecord || $this->id != $existingRecord->id)
      $this->addError('id_no', 'ID Number already exists in the system');
  }

  public function maldivianValidations($attribute) {
    if ($this->country_id != Constants::MALDIVES_COUNTRY_ID)
      return;
    switch ($attribute) {
      case 'id_no':
        if (!preg_match(Constants::ID_CARD_PATTERN, $this->$attribute))
          $this->addError($attribute, 'Maldivian ID Card number is not valid');
        break;
      case 'perm_address_island_id':
        if (empty($this->$attribute))
          $this->addError($attribute, 'Permanent Island cannot be blank');
        break;
    }
  }

  public function checkThreeNames() {
    if (!empty($this->three_names_english) || !empty($this->three_names_arabic)) {
      $this->three_names_english = str_ireplace('abduh ', 'Abdul ',
        $this->three_names_english);
      $this->three_names_english = str_ireplace('abduh', 'Abdul ',
        $this->three_names_english);
//      $this->three_names_english = str_ireplace('abdul ', 'Abdul ',
//        $this->three_names_english);
      $this->three_names_english = trim($this->three_names_english);
      if (str_word_count($this->three_names_english, 0) <= 2)
        $this->addError('three_names_english',
          'Three Names English is incomplete.');

//      $this->three_names_arabic = str_ireplace('عبد ','عبد',
//        $this->three_names_arabic);
      $this->three_names_arabic = trim($this->three_names_arabic);
      if (count(preg_split('~[^\p{L}\p{N}\p{Mn}\x{2019}\'\-]+~u',
          $this->three_names_arabic))
        <= 2
      )
        $this->addError('three_names_arabic',
          'Three Names Arabic is incomplete.');
    }
  }

  public function registerMember(bool $fardHajjPerformed) {
    if (!empty($this->member))
      return True;
    if (!$this->dnr_verified)
      return False;

    #remove any existing forms          
    if (!empty($existingHajjForm = ApplicationForms::model()->findByAttributes(['id_no'=>$this->id_no])))
    {
      if (!empty($existingVerification = ApplicationFormVerifications::model()->findByAttributes(['application_form_id' => $existingHajjForm->id])))
        $existingVerification->delete();            
      $existingHajjForm->delete();          
    }

    $hajjForm = new ApplicationFormsHelper();
    $hajjForm->setAttributes([
      'application_date' => Yii::app()->params['date'],
      'applicant_full_name_english' => $this->full_name_english,
      'applicant_full_name_dhivehi' => $this->full_name_dhivehi,
      'id_no' => $this->id_no, 'd_o_b' => $this->d_o_b,
      'applicant_gender_id' => $this->gender_id,
      'country_id' => $this->country_id,
      'perm_address_island_id' => $this->perm_address_island_id,
      'perm_address_english' => $this->perm_address_english,
      'perm_address_dhivehi' => $this->perm_address_dhivehi,
      'phone_number_1' => $this->phone,
      'email_address' => $this->email,
      'id_copy' => $this->idCopy,
      'emergency_contact_full_name_dhivehi' => '-',
      'emergency_contact_phone_no' => '-',
      'badhal_hajj' => (int)$fardHajjPerformed,
      'application_form' => 'not_required_anymore.png',
    ]);

    $hajjForm->state_id = Constants::APPLICATION_REGISTERED;
    try {
      if (!$hajjForm->save())
        throw new CException("Registration of " . $this->id_no . " validation errors: " .  CJSON::encode($hajjForm->errors));
    } catch (CException $ex) {
      ErrorLog::exceptionLog($ex);
      return False;
    }

    $verifyModel = new ApplicationFormVerifications();
    $verifyModel->application_form_id = $hajjForm->id;
    $verifyModel->applicant_verified = 1;
    $verifyModel->save(false);

    $member = new Members();
    $member->setAttributes([
      'person_id' => $this->id,
      'application_form_id' => $hajjForm->id,
      'phone_number_1' => $this->phone,
      'email_address' => $this->email,
      'badhal_hajj' => $hajjForm->badhal_hajj,
      'mhc_no' => Members::generateMemberNumber(),
      'membership_date' => Yii::app()->params['date'],
      'state_id' => Constants::MEMBER_PENDING_FIRST_PAYMENT
    ]);

    try {
      if (!$member->save())
        throw new CException("Member model save errors: " . CJSON::encode($member->errors));
      Helpers::textMessage($this->phone, $this->full_name_english . H::t('site', 'faheliMemberRegistered'));
    } catch (CException $ex) {
      ErrorLog::exceptionLog($ex);
      return False;
    }
    return True;
    
  }

  public function dnrVerifyPerson() {
    if ($this->dnr_verified != 1) {
      if (False != ($dnr_data = Helpers::getDnrRecord($this->id_no, $this->full_name_english))) {
        $this->full_name_english = $dnr_data->full_name_english;
        $this->full_name_dhivehi = $dnr_data->full_name_dhivehi;
        $this->perm_address_english = $dnr_data->addressEn;
        $this->perm_address_dhivehi = $dnr_data->addressMv;
        $this->perm_address_island_id = $dnr_data->island_id;
        $this->d_o_b = $dnr_data->d_o_b;
        $this->gender_id = $dnr_data->gender_id;
        $this->country_id = Constants::MALDIVES_COUNTRY_ID;
        $this->dnr_verified = 1;
        $this->save(false);
      }
    }
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
      'ageegas' => [self::HAS_MANY, 'Ageega', 'person_id'],
      'employees' => [self::HAS_MANY, 'Employees', 'person_id'],
      'member' => [self::HAS_ONE, 'Members', 'person_id'],
      'umraPilgrim' => [self::HAS_ONE, 'UmraPilgrims', 'person_id'],
      'umraPilgrims' => [self::HAS_MANY, 'UmraPilgrims', 'person_id'],
      'nonMember' => [self::HAS_ONE, 'NonMhclMembers', 'person_id'],
      'members' => [self::HAS_MANY, 'Members', 'caretaker_person_id'],
      'members1' => [self::HAS_MANY, 'Members', 'emergency_contact_person_id'],
      'members2' => [self::HAS_MANY, 'Members', 'person_id'],
      'members3' => [self::HAS_MANY, 'Members', 'replacement_person_id'],
      'members4' => [self::HAS_MANY, 'Members', 'vaarutha_person_id'],
      'nonMhclMembers' => [self::HAS_MANY, 'NonMhclMembers', 'person_id'],
      'organizationStaff' => [self::HAS_MANY, 'OrganizationStaff', 'person_id'],
      'gender' => [self::BELONGS_TO, 'ZGender', 'gender_id'],
      'permAddressIsland' => [self::BELONGS_TO, 'ZIslands', 'perm_address_island_id'],
      'country' => [self::BELONGS_TO, 'ZCountry', 'country_id'],
      'users' => [self::HAS_MANY, 'Users', 'person_id'],
      'user' => [self::HAS_ONE, 'Users', 'person_id'],
      'machineEmployee' => [self::HAS_ONE, 'MachineEmployees', 'person_id'],
      'personLogin' => [self::HAS_ONE, 'PersonLogin', 'person_id'],
    ];
  }

  public function getNameParts() {
    $name = str_replace(['   ','  '],[' ',' '],$this->three_names_english);
    $names = explode(' ', $name);
    $firstName = array_shift($names);
    $lastName = array_pop($names);
    $middleNames = implode(' ', $names);
    return compact('firstName','middleNames', 'lastName');
  }

  public function getOnlineRegistrationForm() {
    return OnlineRegistrationForm::model()->findByAttributes([
      'country_id' => $this->country_id, 'id_no' => $this->id_no
    ]);
  }
  public function getHajjApplicationForm() {
    return ApplicationForms::model()->findByAttributes([
      'country_id' => $this->country_id, 'id_no' => $this->id_no
    ]);
  }

  public function getIdCopy() {
    if (!empty($this->onlineRegistrationForm))
      return $this->onlineRegistrationForm->id_card_copy;
    if (!empty($this->member))
      return $this->member->applicationForm->id_copy;
    if (!empty($this->umraPilgrim))
      return $this->umraPilgrim->id_copy;
  }

  public function getPhotoUrl() {
    if (!empty($this->latestPassport)) {
      return Yii::app()->params['passportService']
      .$this->latestPassport->pp_copy;
    } elseif (!empty($this->member) && !empty($this->member->applicationForm)
    && !empty($this->member->applicationForm->passport_photo)) {
      return Helpers::sysUrl(Constants::UPLOADS) .
      $this->member->applicationForm->passport_photo;
    } else
      return null;
  }
  public function getPhone() {
    if (!empty($this->personLogin))
      return $this->personLogin->mobile;
    if (!empty($this->onlineRegistrationForm))
      return $this->onlineRegistrationForm->phone_number_1;
    if (!empty($this->member))
      return $this->member->phone_number_1;
    if (!empty($this->umraPilgrim))
      return $this->umraPilgrim->phone_number;
    if (!empty($this->nonMember))
      return $this->nonMember->phone_number;
    if (!empty($this->users))
      return $this->user->mobile_number;
    return '';
  }
  public function getEmail() {
    $email = null;
    if (!empty($this->onlineRegistrationForm) && !empty
      ($this->onlineRegistrationForm->email_address))
      return $this->onlineRegistrationForm->email_address;
    if (!empty($this->member) && !empty($this->member->email_address))
      return $this->member->email_address;
    if (!empty($this->umraPilgrim) && !empty($this->umraPilgrim->email_address))
      return $this->umraPilgrim->email_address;
    if (!empty($this->user) && !empty($this->user->email))
      return $this->user->email;
    return '';
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
      'id' => 'ID',
      'id_no' => 'ID Card',
      'full_name_english' => 'Full Name',
      'full_name_dhivehi' => 'ފުރިހަމަ ނަން',
      'three_names_english' => 'English Three Names',
      'three_names_arabic' => 'Arabic Three Names',
      'd_o_b' => 'D.O.B',
      'perm_address_island_id' => 'Island',
      'gender_id' => 'Gender',
      'perm_address_english' => 'House Name, Street',
      'perm_address_dhivehi' => 'ގޭގެ ނަން، މަގު',
      'dnr_verified' => 'DNR Verified',
      'country_id' => 'Country',
      'photo_file_id' => 'Photo',
      'operation_log_id' => 'Operation Log',
    ];
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   *
   * Typical usecase:
   * - Initialize the model fields with values from filter form.
   * - Execute this method to get CActiveDataProvider instance which will filter
   * models according to data in model fields.
   * - Pass data provider to CGridView, CListView or any similar widget.
   *
   * @return CActiveDataProvider the data provider that can return the models
   * based on the search/filter conditions.
   */
  public function search() {
    // @todo Please modify the following code to remove attributes that should not be searched.

    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('id_no', $this->id_no, true);
    $criteria->compare('full_name_english', $this->full_name_english, true);
    $criteria->compare('full_name_dhivehi', $this->full_name_dhivehi, true);
    $criteria->compare('three_names_english', $this->three_names_english, true);
    $criteria->compare('three_names_arabic', $this->three_names_arabic, true);
    $criteria->compare('d_o_b', $this->d_o_b, true);
    $criteria->compare('perm_address_island_id', $this->perm_address_island_id);
    $criteria->compare('gender_id', $this->gender_id);
    $criteria->compare('perm_address_english', $this->perm_address_english, true);
    $criteria->compare('perm_address_dhivehi', $this->perm_address_dhivehi, true);
    $criteria->compare('photo_file_id', $this->photo_file_id);
    $criteria->compare('operation_log_id', $this->operation_log_id);

    return new CActiveDataProvider($this, [
      'criteria' => $criteria,
    ]);
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your
   * CActiveRecord descendants!
   *
   * @param string $className active record class name.
   *
   * @return Persons the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public function behaviors() {
    return [
      // Classname => path to Class
      'ActiveRecordDateBehavior' =>
        'application.behaviors.ActiveRecordDateBehavior',
    ];
  }

  public function afterFind() {
    foreach ($this->attributeNames() as $attribute) {
      if (strtolower(substr($attribute, -7)) == 'english')
        $this->$attribute = ucwords($this->$attribute);
    }
    parent::afterFind();
  }

  public function afterSave() {
    $engNames = explode(' ', trim($this->full_name_english));
    $dhiNames = explode(' ', trim($this->full_name_dhivehi));
    if (sizeof($engNames) == sizeof($dhiNames)) {
      foreach ($engNames as $k=>$v) {
        $existingNameMatch = NamesList::model()->findByAttributes([
          'name_english' => $v,
          'name_dhivehi' => $dhiNames[$k]
        ]);
        if (!empty($existingNameMatch)) {
          ++$existingNameMatch->occurance;
          $existingNameMatch->save();
        } else {
          $newNamePair = new NamesList();
          $newNamePair->name_english = $v;
          $newNamePair->name_dhivehi = $dhiNames[$k];
          $newNamePair->occurance = 1;
          $newNamePair->save();
        }
      }
    }

    parent::afterSave();
  }

  public function getIdName() {
    return $this->id_no . ', ' . $this->full_name_english;
  }

  public function getLatestPassport() {
    $criteria = new CDbCriteria();
    $criteria->compare('id_no', $this->id_no);
    $criteria->addNotInCondition('preferred_passport', [0]);
    $criteria->order = "expiry desc";
    $criteria->limit = 1;

    return Passports::model()->find($criteria);
  }

  public function getPersonText() {

    $name = $this->full_name_english;
    $id = $this->id_no;
    $pAdd = $this->permAddressText;
    $age = 'Age: ' . $this->ageNow;

    return implode(', ', array_filter([$name, $id, $pAdd, $age]));
  }

  public function getPermAddressText() {
    return implode(', ', array_filter([($this->country_id == Constants::MALDIVES_COUNTRY_ID ?
      $this->atollIsland : null), $this->perm_address_english, $this->country->name]));
  }

  public function getPermAddressTextDhivehi() {
    return implode('، ', array_filter([$this->perm_address_dhivehi,
      ($this->country_id == Constants::MALDIVES_COUNTRY_ID ?
        $this->getAtollIslandDhivehi() : null)]));
  }

  public function getAtollIsland() {
    return (!empty($this->perm_address_island_id)) ? ($this->permAddressIsland->atoll->abbreviation_english . ". " . $this->permAddressIsland->name_english) : '';
  }

  public function getAtollIslandDhivehi() {
    return (!empty($this->perm_address_island_id)) ? ($this->permAddressIsland->atoll->abbreviation_dhivehi . ". " . $this->permAddressIsland->name_dhivehi) : '';
  }

  public function getAgeNow() {
    return Helpers::age($this->d_o_b);
  }
}
