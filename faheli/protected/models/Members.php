<?php

/**
 * This is the model class for table "members".
 *
 * The followings are the available columns in table 'members':
 * @property integer $id
 * @property integer $mhc_no
 * @property integer $state_id
 * @property integer $person_id
 * @property string $current_address_english
 * @property string $current_address_dhivehi
 * @property string $current_island_id
 * @property string $phone_number_1
 * @property string $email_address
 * @property integer $marital_status
 * @property integer $number_of_children
 * @property string $family_contact_no
 * @property integer $vaarutha_person_id
 * @property string $vaarutha_phone_number
 * @property integer $emergency_contact_person_id
 * @property string $emergency_contact_full_name_english
 * @property string $emergency_contact_current_address_english
 * @property string $emergency_contact_current_address_dhivehi
 * @property string $emergency_contact_phone_no
 * @property string $employed_institution_name_english
 * @property string $employed_institution_name_dhivehi
 * @property string $employed_institution_address_english
 * @property string $employed_institution_address_dhivehi
 * @property string $employed_designation_english
 * @property string $employed_designation_dhivehi
 * @property string $employed_institution_phone_number_1
 * @property integer $employed_salary
 * @property string $previous_hajj_year
 * @property string $previous_hajj_year_badhal
 * @property string $previous_hajj_year_badhal_for
 * @property integer $preferred_year_for_hajj
 * @property boolean $badhal_hajj
 * @property string $badhal_hajj_person_full_name_english
 * @property string $badhal_hajj_person_full_name_dhivehi
 * @property string $group_name
 * @property integer $mahram_member_id
 * @property string $mahram_relationship_english
 * @property string $mahram_relationship_dhivehi
 * @property string $mahram_phone_no
 * @property integer $package_preference
 * @property integer $deposit_preference
 * @property integer $replacement_person_id
 * @property string $replacement_relationship_english
 * @property string $replacement_relationship_dhivehi
 * @property integer $caretaker_person_id
 * @property string $caretaker_full_name_english
 * @property string $caretaker_current_address_english
 * @property string $caretaker_current_address_dhivehi
 * @property string $caretaker_phone_number_1
 * @property string $caretaker_relationship_english
 * @property string $caretaker_relationship_dhivehi
 * @property string $membership_date
 * @property integer $application_form_id
 * @property integer $operation_log_id
 *
 * Calculated
 * @property string $memberPersonalInfoText
 * @property string $familyInfoText
 * @property string $emergencyContactInfoText
 * @property string $hajjInfoText
 * @property string $caretakerInfoText
 * @property integer $groupCount
 *
 * The followings are the available model relations:
 * @property string $MHC_ID
 * @property HotelRooms[] $hotelRooms
 * @property MemberLogin $memberLogin
 * @property array $hajjPlacement;
 * @property MemberTransactions[] $memberTransactions
 * @property ApplicationFormsHelper $applicationForm
 * @property Persons $caretakerPerson
 * @property ZDepositPreference $depositPreference
 * @property Persons $emergencyContactPerson
 * @property Members $mahramMember
 * @property Persons $mahramPerson
 * @property Members[] $members
 * @property ZMaritalStatuses $maritalStatus
 * @property ZPackagePreferences $packagePreference
 * @property Persons $person
 * @property Persons $replacementPerson
 * @property ZMemberStates $state
 * @property Persons $vaaruthaPerson
 * @property TrainingModuleAllocations[] $trainingModuleAllocations
 * @property float $accountBalance
 */
class Members extends CActiveRecord {

  public $balance;
  public $last_payment_date;
  public $matured_balance;
  public $matured_date;

  /**
   * @return integer
   */
  public function getGroupCount() {
    if (!empty($this->group_name))
    {
      $groupName = $this->group_name;
      return Yii::app()->db->createCommand("Select count(id) from members where group_name = :groupName")
        ->bindParam(':groupName', $groupName, PDO::PARAM_STR)->queryScalar();
    }
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return "members";
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
        ['person_id, application_form_id', 'required'],
        ['person_id', 'unique', 'message' => 'The person is already registered'],
        ['mhc_no, state_id, person_id, marital_status, number_of_children,
          vaarutha_person_id, emergency_contact_person_id, employed_salary,
          preferred_year_for_hajj, badhal_hajj, mahram_member_id,
          package_preference, deposit_preference, replacement_person_id,
          caretaker_person_id, application_form_id, operation_log_id',
          'numerical', 'integerOnly' => true],
        ['current_address_english, current_address_dhivehi, email_address,
          emergency_contact_current_address_english,
          emergency_contact_current_address_dhivehi,
          employed_institution_name_english, employed_institution_name_dhivehi,
          employed_institution_address_english,
          employed_institution_address_dhivehi,
          employed_designation_english, employed_designation_dhivehi,
          badhal_hajj_person_full_name_english,
          badhal_hajj_person_full_name_dhivehi, group_name,
          caretaker_current_address_english, caretaker_current_address_dhivehi',
          'length', 'max' => 255],
        ['phone_number_1, family_contact_no, vaarutha_phone_number,
        emergency_contact_phone_no, employed_institution_phone_number_1,
        mahram_phone_no, caretaker_phone_number_1', 'length', 'max' => 25],
        ['mahram_relationship_english, mahram_relationship_dhivehi,
        replacement_relationship_english, replacement_relationship_dhivehi,
        caretaker_relationship_english, caretaker_relationship_dhivehi',
          'length', 'max' => 100],
        ['membership_date', 'safe'],
    ];
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
        'hotelRooms' => [self::MANY_MANY, 'HotelRooms', 'hotel_room_allocations(member_id, hotel_room_id)'],
        'memberTransactions' => [self::HAS_MANY, 'MemberTransactions', 'member_id'],
        'accountBalance' => [self::STAT, 'MemberTransactions', 'member_id', 'select' => 'SUM(amount)', 'condition' => 'is_cancelled = 0'],
        'applicationForm' => [self::BELONGS_TO, 'ApplicationFormsHelper',
          'application_form_id'],
        'caretakerPerson' => [self::BELONGS_TO, 'Persons', 'caretaker_person_id'],
        'depositPreference' => [self::BELONGS_TO, 'ZDepositPreference', 'deposit_preference'],
        'emergencyContactPerson' => [self::BELONGS_TO, 'Persons', 'emergency_contact_person_id'],
        'mahramMember' => [self::BELONGS_TO, 'Members', 'mahram_member_id'],
        'members' => [self::HAS_MANY, 'Members', 'mahram_member_id'],
        'maritalStatus' => [self::BELONGS_TO, 'ZMaritalStatuses', 'marital_status'],
        'packagePreference' => [self::BELONGS_TO, 'ZPackagePreferences', 'package_preference'],
        'person' => [self::BELONGS_TO, 'Persons', 'person_id'],
        'replacementPerson' => [self::BELONGS_TO, 'Persons', 'replacement_person_id'],
        'state' => [self::BELONGS_TO, 'ZMemberStates', 'state_id'],
        'vaaruthaPerson' => [self::BELONGS_TO, 'Persons', 'vaarutha_person_id'],
        'trainingModuleAllocations' => [self::HAS_MANY, 'TrainingModuleAllocations', 'member_id'],
        'operationLog' => [self::BELONGS_TO, 'OperationLogs', 'operation_log_id'],
        'memberLogin' => [self::HAS_ONE, 'MemberLogin',
                          'member_id'],
    ];

  }

  public function getId_Name() {
    return $this->person->id_no . ', ' . $this->person->full_name_english;
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
        'id' => 'ID',
        'mhc_no' => 'Mhc No',
        'state_id' => 'State',
        'person_id' => 'Person',
        'current_island_id' => 'Current Island',
        'current_address_english' => 'House, Street, Atoll, Island',
        'current_address_dhivehi' => 'ގޭގެނަން، މަގު، އަތޮޅު، ރަށް',
        'phone_number_1' => 'Phone',
        'email_address' => 'Email',
        'marital_status' => 'Marital Status',
        'number_of_children' => 'Number Of Children',
        'family_contact_no' => 'Family Contact No',
        'vaarutha_person_id' => 'Vaarutha Person',
        'vaarutha_phone_number' => 'Phone',
        'emergency_contact_person_id' => 'Emergency Contact Person',
        'emergency_contact_current_address_english' => 'House, Street, Atoll, Island',
        'emergency_contact_current_address_dhivehi' => 'ގޭގެނަން، މަގު، އަތޮޅު، ރަށް',
        'emergency_contact_phone_no' => 'Phone',
        'employed_institution_name_english' => 'Office',
        'employed_institution_name_dhivehi' => 'އޮފީސް',
        'employed_institution_address_english' => 'Building Name, Street, Atoll, Island',
        'employed_institution_address_dhivehi' => 'އިމާރާތުގެ ނަމާއި، މަގު، އަތޮޅު، ރަށް',
        'employed_designation_english' => 'Designation',
        'employed_designation_dhivehi' => 'މަޤާމު',
        'employed_institution_phone_number_1' => 'Phone',
        'employed_salary' => 'Salary in Rufiya',
        'previous_hajj_year' => 'Previous Hajj Years',
        'preferred_year_for_hajj' => 'Requested Year',
        'badhal_hajj' => 'Badhalu Hajj',
        'badhal_hajj_person_full_name_english' => 'Full Name',
        'badhal_hajj_person_full_name_dhivehi' => 'ފުރިހަމަ ނަން',
        'group_name' => 'Group Main ID No',
        'mahram_member_id' => 'Mahram Member',
        'mahram_relationship_english' => 'Relationship',
        'mahram_relationship_dhivehi' => 'ހުރި ގުޅުން',
        'mahram_mhc_membership_no' => 'MHC No.',
        'package_preference' => 'Package',
        'deposit_preference' => 'Deposit to',
        'replacement_person_id' => 'Replacement Person',
        'replacement_relationship_english' => 'Relationship',
        'replacement_relationship_dhivehi' => 'ހުރި ގުޅުން',
        'caretaker_person_id' => 'Caretaker Person',
        'caretaker_current_address_english' => 'House Name, Street, Atoll, Island',
        'caretaker_current_address_dhivehi' => 'ގޭގެ ނަން، މަގު، އަތޮޅު، ރަށް',
        'caretaker_phone_number_1' => 'Phone',
        'caretaker_relationship_english' => 'Relationship',
        'caretaker_relationship_dhivehi' => 'ހުރި ގުޅުން',
        'membership_date' => 'Membership Date',
        'application_form_id' => 'Application Form',
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
    $criteria->compare('mhc_no', $this->mhc_no);
    $criteria->compare('state_id', $this->state_id);
    $criteria->compare('person_id', $this->person_id);
    $criteria->compare('current_island_id', $this->current_island_id);
    $criteria->compare('current_address_english', $this->current_address_english, true);
    $criteria->compare('current_address_dhivehi', $this->current_address_dhivehi, true);
    $criteria->compare('phone_number_1', $this->phone_number_1, true);
    $criteria->compare('email_address', $this->email_address, true);
    $criteria->compare('marital_status', $this->marital_status);
    $criteria->compare('number_of_children', $this->number_of_children);
    $criteria->compare('family_contact_no', $this->family_contact_no, true);
    $criteria->compare('vaarutha_person_id', $this->vaarutha_person_id);
    $criteria->compare('vaarutha_phone_number', $this->vaarutha_phone_number, true);
    $criteria->compare('emergency_contact_person_id', $this->emergency_contact_person_id);
    $criteria->compare('emergency_contact_current_address_english', $this->emergency_contact_current_address_english, true);
    $criteria->compare('emergency_contact_current_address_dhivehi', $this->emergency_contact_current_address_dhivehi, true);
    $criteria->compare('emergency_contact_phone_no', $this->emergency_contact_phone_no, true);
    $criteria->compare('employed_institution_name_english', $this->employed_institution_name_english, true);
    $criteria->compare('employed_institution_name_dhivehi', $this->employed_institution_name_dhivehi, true);
    $criteria->compare('employed_institution_address_english', $this->employed_institution_address_english, true);
    $criteria->compare('employed_institution_address_dhivehi', $this->employed_institution_address_dhivehi, true);
    $criteria->compare('employed_designation_english', $this->employed_designation_english, true);
    $criteria->compare('employed_designation_dhivehi', $this->employed_designation_dhivehi, true);
    $criteria->compare('employed_institution_phone_number_1', $this->employed_institution_phone_number_1, true);
    $criteria->compare('employed_salary', $this->employed_salary);
    $criteria->compare('previous_hajj_year', $this->previous_hajj_year);
    $criteria->compare('preferred_year_for_hajj', $this->preferred_year_for_hajj);
    $criteria->compare('mahram_member_id', $this->mahram_member_id);
    $criteria->compare('mahram_relationship_english', $this->mahram_relationship_english, true);
    $criteria->compare('mahram_relationship_dhivehi', $this->mahram_relationship_dhivehi, true);
    $criteria->compare('mahram_phone_no', $this->mahram_phone_no, true);
    $criteria->compare('package_preference', $this->package_preference);
    $criteria->compare('deposit_preference', $this->deposit_preference);
    $criteria->compare('replacement_person_id', $this->replacement_person_id);
    $criteria->compare('replacement_relationship_english', $this->replacement_relationship_english, true);
    $criteria->compare('replacement_relationship_dhivehi', $this->replacement_relationship_dhivehi, true);
    $criteria->compare('caretaker_person_id', $this->caretaker_person_id);
    $criteria->compare('caretaker_current_address_english', $this->caretaker_current_address_english, true);
    $criteria->compare('caretaker_current_address_dhivehi', $this->caretaker_current_address_dhivehi, true);
    $criteria->compare('caretaker_phone_number_1', $this->caretaker_phone_number_1, true);
    $criteria->compare('caretaker_relationship_english', $this->caretaker_relationship_english, true);
    $criteria->compare('caretaker_relationship_dhivehi', $this->caretaker_relationship_dhivehi, true);
    $criteria->compare('membership_date', $this->membership_date, true);
    $criteria->compare('application_form_id', $this->application_form_id);
    $criteria->compare('operation_log_id', $this->operation_log_id);

    return new CActiveDataProvider($this, [
        'criteria' => $criteria,
    ]);
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Members the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public static function generateMemberNumber() {
    $memberCount = Yii::app()->db->createCommand('select count(id) from members where mhc_no IS NOT NULL AND mhc_no <> 0')->queryScalar();
    return $memberCount + 1;
  }

  public function getMHC_ID() {
    $prefix = Helpers::config('mhcIDPrefix');
    $digits = Helpers::config('mhcIDDigitCount');
    $suffix = Helpers::config('mhcIDSuffixYear');
    $separator = Helpers::config('mhcIDSeparator');
    return (!empty($prefix) ? ($prefix . $separator) : '') . str_pad($this->mhc_no, $digits, "0", STR_PAD_LEFT)
            . ($suffix ? ($separator . date('Y', strtotime($this->membership_date))) : '');
  }

  public function getMemberPersonalInfoText() {
    return implode(', ', array_filter([
        $this->person->getPersonText(),
        $this->phone_number_1,
        $this->email_address
    ]));
  }

  public function getFamilyInfoText() {
    return implode(', ', array_filter([
      (empty($this->maritalStatus)?"":$this->maritalStatus->name_english),
      $this->family_contact_no,
    ]));
  }

  public function getEmergencyContactInfoText() {
    return implode(', ', array_filter([
      $this->emergency_contact_full_name_english,
      $this->emergency_contact_phone_no
    ]));
  }

  public function getJobInfoText() {
    if (!empty($this->employed_institution_name_english)) {
      return implode(', ', array_filter([
          $this->employed_designation_english,
          $this->employed_institution_name_english,
          $this->employed_institution_address_english,
          $this->employed_institution_phone_number_1,
          'MVR ' . number_format($this->employed_salary, 0, '.', ',')
      ]));
    } else {
      return 'N/A';
    }
  }

  public function getHajjInfoText() {
    return implode(', ', array_filter([
//      'Previous Hajjs: ' . (empty($this->previous_hajj_year) ? 'None' :
//        ($this->previous_hajj_year)),
//      'Previous Badhal Hajjs: ' . (empty($this->previous_hajj_year_badhal) ? 'None' : ($this->previous_hajj_year_badhal .
//        ' (for ' . $this->previous_hajj_year_badhal_for . ')')) . ', ',
      'Requested Year: ' .
      (empty($this->preferred_year_for_hajj) ?
        'N/A' :
        $this->preferred_year_for_hajj),
      (empty($this->badhal_hajj) ?
        '' :
        ('Registered for a Badhal Hajj')),
    ]));
  }

  public function getMahramInfoText() {
    return 'refer to Application Form';
  }

  public function getDepositsInfoText() {
    return 'N/A';
//    return implode(', ', array_filter([
//      $this->packagePreference->name_english,
//      $this->depositPreference->name_english
//    ]));
  }

  public function getHajjPlacement() {
    $placements = file_get_contents(Yii::app()->basePath .
      "/../../". $GLOBALS['cfg']['fileUploadParentFolder']
      ."/files/lists/hajjiList.json");
    if (!empty($placements))
      foreach(CJSON::decode($placements) as $placement) {
        if (!empty($placement['id']) &&
          $placement['id'] == $this->person->id_no) {
          return $placement;
        }
      }
      return null;


  }

  public function getReplacementInfoText() {
    return 'refer to Application Form';
  }

  public function getCaretakerInfoText() {
    if (!empty($this->caretaker_full_name_english)) {
      return implode(', ', array_filter([
        $this->caretaker_full_name_english,
        $this->caretaker_phone_number_1
      ]));
    } else {
      return '';
    }
  }

  public function getWitnessInfoText() {
    return 'Refer to Form';
  }

  public function beforeSave() {
    // ensure application state is updated before member is saved as a normal member
    if ($this->state_id == Constants::MEMBER_NORMAL) {
      $appForm = $this->applicationForm;
      $appForm->state_id = Constants::APPLICATION_REGISTERED;
      $appForm->save();
    }

    // replace empty fields with null
    if (empty($this->previous_hajj_year))
      $this->previous_hajj_year = null;
    if (empty($this->previous_hajj_year_badhal))
      $this->previous_hajj_year_badhal = null;
    if (empty($this->previous_hajj_year_badhal_for))
      $this->previous_hajj_year_badhal_for = null;

    return parent::beforeSave();
  }

  public function afterFind() {
    foreach ($this->attributeNames() as $attribute) {
      if (strtolower(substr($attribute, -7)) == 'english')
        $this->$attribute = ucwords($this->$attribute);
    }
    parent::afterFind();
  }

  public function behaviors() {
    return [
        'ActiveRecordDateBehavior' =>
        'application.behaviors.ActiveRecordDateBehavior',
    ];
  }

}
