<?php

/**
 * This is the model class for table "online_registration_form".
 *
 * The followings are the available columns in table 'online_registration_form':
 *
 * @property integer  $id
 * @property string   $id_no
 * @property string   $full_name_english
 * @property string   $full_name_dhivehi
 * @property string   $three_names_english
 * @property string   $three_names_arabic
 * @property string   $d_o_b
 * @property integer  $perm_address_island_id
 * @property integer  $gender_id
 * @property string   $perm_address_english
 * @property string   $perm_address_dhivehi
 * @property string   $country_id
 * @property string   $id_card_copy
 * @property string   $phone_number_1
 * @property string   $email_address
 * @property string   $submitted_date_time
 * @property integer  $approved
 * @property integer  $cancelled_by_user_id
 * @property string   $cancelled_reason
 * @property string   $cancelled_date_time
 * @Property string   $hash_key
 * @property integer  $operation_log_id
 *
 * Calculated properties / relations
 * @property Persons  $person
 * @property string   $atollIslandDhivehi
 * @property string   $atollIsland
 *
 * The followings are the available model relations:
 * @property Users    $cancelledByUser
 * @property ZGender  $gender
 * @property ZCountry $country
 * @property ZIslands $permAddressIsland
 */
class OnlineRegistrationForm extends CActiveRecord {
  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'online_registration_form';
  }


  public function getAtollIslandDhivehi() {
    return (!empty($this->perm_address_island_id)) ? ($this->permAddressIsland->atoll->abbreviation_dhivehi .
      ". " . $this->permAddressIsland->name_dhivehi) : '';
  }

  public function getAtollIsland() {
    return (!empty($this->perm_address_island_id)) ? ($this->permAddressIsland->atoll->abbreviation_english .
      ". " . $this->permAddressIsland->name_english) : '';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
      ['id_no', 'existingRegistration'],[
        'id_no, full_name_english, full_name_dhivehi, d_o_b, gender_id,
			phone_number_1, perm_address_english,
			perm_address_dhivehi', 'required',
        'message' => '{attribute}' . H::t('site', 'required')
      ],
      ['perm_address_island_id, id_no', 'maldivianValidations'],
      ['id_card_copy', 'required', 'message' => '{attribute}' . H::t('site', 'attach')],
      [
        'perm_address_island_id, gender_id, approved, cancelled_by_user_id,
			  operation_log_id', 'numerical', 'integerOnly' => true
      ], ['id_no', 'length', 'max' => 30], [
        'full_name_english, full_name_dhivehi, three_names_english,
        three_names_arabic, perm_address_english, cancelled_reason,
        perm_address_dhivehi',
        'length', 'max' => 255, 'tooLong' => '{attribute} ކުރު ކުރައްވާ'
      ], ['country_id', 'length', 'max' => 3],
      ['phone_number_1', 'ext.validators.mobilePhoneNumber'],
      ['email_address', 'email', 'message' => '{attribute}' . H::t('site','isWrong')],
      ['submitted_date_time, cancelled_date_time, hash_key', 'safe'],
    ];
  }

  public function maldivianValidations($attribute) {
    if ($this->country_id != Constants::MALDIVES_COUNTRY_ID) {
      return;
    }
    switch ($attribute) {
      case 'id_no':
        if (!empty($this->id_no) &&
          !preg_match(Constants::ID_CARD_PATTERN, $this->$attribute)) {
          $this->addError($attribute, $this->getAttributeLabel($attribute) .
            H::t('site', 'isWrong'));
        }
        break;
      case 'perm_address_island_id':
        if (empty($this->$attribute)) {
          $this->addError($attribute, H::t('site','permIslandRequired'));
        }
        break;
    }
  }

  public function existingRegistration() {
    /** @var OnlineRegistrationForm $existingRecord */
    $existingRecord = self::model()->find([
      'condition' => 'id_no = :idNo AND country_id = :maldives AND cancelled_by_user_id IS NULL',
      'params'=>[
        'idNo' => $this->id_no,
        'maldives' => Constants::MALDIVES_COUNTRY_ID,
      ]]);
    if (empty($existingRecord)) {
      return;
    }

    if ($this->isNewRecord || $this->id != $existingRecord->id) {
      if (!empty($existingRecord->person) && $existingRecord->approved) {
        $this->addError('id_no', H::t('site','idAlreadyRegistered'));
      } else {
        $this->addError('id_no', H::t('site','idAlreadyInRegistration'));
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
      'gender' => [self::BELONGS_TO, 'ZGender', 'gender_id'],
      'permAddressIsland' => [
        self::BELONGS_TO, 'ZIslands', 'perm_address_island_id'
      ], 'country' => [self::BELONGS_TO, 'ZCountry', 'country_id'],
      'cancelledByUser' => [self::BELONGS_TO, 'Users', 'cancelled_by_user_id'],
      'operationLog' => [self::BELONGS_TO, 'OperationLogs', 'operation_log_id'],
    ];
  }

  public function getPerson() {
    return Persons::model()->findByAttributes([
      'country_id' => $this->country_id, 'id_no' => $this->id_no
    ]);
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
      'id_no' => H::t('site','id_no'),
      'full_name_english' => H::t('site','full_name_english'),
      'full_name_dhivehi' => H::t('site','full_name_dhivehi'),
      'perm_address_island_id' => H::t('site','perm_address_island_id'),
      'd_o_b' => H::t('site','d_o_b'),
      'gender_id' => H::t('site','gender_id'),
      'perm_address_english' => H::t('site','perm_address_english'),
      'perm_address_dhivehi' => H::t('site','perm_address_dhivehi'),
      'country_id' => H::t('site','country_id'),
      'id_card_copy' => H::t('site','id_card_copy'),
      'phone_number_1' => H::t('site','mobile'),
      'email_address' => H::t('site','email_address'),
			'submitted_date_time' => H::t('site','submitted_date_time'),
      'operation_log_id' => 'Operation Log',
    ];
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   *
   * @param string $className active record class name.
   *
   * @return OnlineRegistrationForm the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public function behaviors() {
    return [
      // Classname => path to Class
      'ActiveRecordDateBehavior' => 'application.behaviors.ActiveRecordDateBehavior',
    ];
  }


}
