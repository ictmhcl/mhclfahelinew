<?php

/**
 * This is the model class for table "training_locations".
 *
 * The followings are the available columns in table 'training_locations':
 * @property integer $id
 * @property integer $training_id
 * @property string $location_name_english
 * @property string $location_name_dhivehi
 * @property string $location_address_english
 * @property string $location_address_dhivehi
 * @property integer $seat_count
 * @property string $from_datetime
 * @property string $till_datetime
 * @property string $instructor_name_english
 * @property string $instructor_name_dhivehi
 *
 * The followings are the available model relations:
 * @property TrainingLocationMembers[] $trainingLocationMembers
 * @property Trainings $training
 */
class TrainingLocations extends CActiveRecord {

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'training_locations';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
        ['training_id, location_name_english, location_name_dhivehi, location_address_english, location_address_dhivehi, seat_count, from_datetime, till_datetime', 'required'],
        ['training_id, seat_count', 'numerical', 'integerOnly' => true],
        ['location_name_english, location_name_dhivehi, location_address_english, location_address_dhivehi, instructor_name_english, instructor_name_dhivehi', 'length', 'max' => 255],
        // The following rule is used by search().
        // @todo Please remove those attributes that should not be searched.
        ['id, training_id, location_name_english, location_name_dhivehi, location_address_english, location_address_dhivehi, seat_count, from_datetime, till_datetime, instructor_name_english, instructor_name_dhivehi', 'safe', 'on' => 'search'],
    ];
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
        'trainingLocationMembers' => [self::HAS_MANY, 'TrainingLocationMembers', 'training_location_id'],
        'training' => [self::BELONGS_TO, 'Trainings', 'training_id'],
    ];
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
        'id' => 'ID',
        'training_id' => 'Training',
        'location_name_english' => 'Location Name',
        'location_name_dhivehi' => 'ނަން',
        'location_address_english' => 'Address',
        'location_address_dhivehi' => 'އެޑްރެސް',
        'seat_count' => 'Seat Count',
        'from_datetime' => 'From',
        'till_datetime' => 'Till',
        'instructor_name_english' => 'Instructor Name',
        'instructor_name_dhivehi' => 'މުދައްރިސް',
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
    $criteria->compare('training_id', $this->training_id);
    $criteria->compare('location_name_english', $this->location_name_english, true);
    $criteria->compare('location_name_dhivehi', $this->location_name_dhivehi, true);
    $criteria->compare('location_address_english', $this->location_address_english, true);
    $criteria->compare('location_address_dhivehi', $this->location_address_dhivehi, true);
    $criteria->compare('seat_count', $this->seat_count);
    $criteria->compare('from_datetime', $this->from_datetime, true);
    $criteria->compare('till_datetime', $this->till_datetime, true);
    $criteria->compare('instructor_name_english', $this->instructor_name_english, true);
    $criteria->compare('instructor_name_dhivehi', $this->instructor_name_dhivehi, true);

    return new CActiveDataProvider($this, [
        'criteria' => $criteria,
    ]);
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return TrainingLocations the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public function getAllotedSeats() {
    $allotedSeats = Yii::app()->db->createCommand('SELECT count(id) FROM training_location_members WHERE training_location_id = ' . $this->id . ' LIMIT 1')->queryScalar();
    return nz($allotedSeats, 0);
  }

  public function behaviors() {
    return [
        'ActiveRecordDateBehavior' =>
        'application.behaviors.ActiveRecordDateBehavior',
    ];
  }

}
