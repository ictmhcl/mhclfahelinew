<?php

/**
 * This is the model class for table "umra_trips".
 *
 * The followings are the available columns in table 'umra_trips':
 *
 * @property integer $id
 * @property string  $name_english
 * @property string  $name_dhivehi
 * @property string  $description_english
 * @property string  $description_dhivehi
 * @property integer $month
 * @property integer $year
 * @property string  $departure_date
 * @property string  $return_date
 * @property integer $completed
 * @property integer $registered
 * @property integer $fullPaid
 * @property integer $withBalance
 * @property string  $deadline_date
 * @property double  $price
 * @property integer $internal_only
 *
 * Computed fields
 * @property string  $tripDate
 * @property UmraTripDiscounts $umraTripDiscounts
 * @property bool    $closed
 * @property UmraPilgrims    $pilgrims
 * @property string  $monthText
 * @property UmraPilgrims|false  $currentPilgrim
 */
class UmraTrips extends CActiveRecord {
  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'umra_trips';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return [
      ['name_english, name_dhivehi, month, year, price', 'required'],
      ['month, year, completed, internal_only', 'numerical', 'integerOnly' =>
        true],
      ['price', 'numerical'], [
        'description_english, description_dhivehi, departure_date, return_date,
			  deadline_date', 'safe'
      ], ['name_english, name_dhivehi', 'length', 'max' => 255],
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      [
        'id, name_english, name_dhivehi, month, year, price', 'safe',
        'on' => 'search'
      ],
    ];
  }

  /**
   * Returns Pilgrim Record if current user is registered for trip otherwise
   * false
   *
   * @return UmraPilgrims|false
   */
  public function getCurrentPilgrim() {
    if (Yii::app()->user->isGuest)
      return false;

    $pilgrims = $this->pilgrims(['condition' => 'person_id = ' . Yii::app()->user->id]);
    return !empty($pilgrims)?$pilgrims[0]:false;
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return [
      'pilgrims' => [self::HAS_MANY, 'UmraPilgrims', 'umra_trip_id'],
      'registered' => [self::STAT, 'UmraPilgrims', 'umra_trip_id'],
      'withBalance' => [
        self::STAT, 'UmraPilgrims', 'umra_trip_id', 'select' => 'count(id)',
        'condition' => 'account_balance > 0'
      ], 'receipts' => [
        self::STAT, 'UmraPilgrims', 'umra_trip_id',
        'select' => 'sum(account_balance)'
      ], 'fullyPaid' => [
        self::STAT, 'UmraPilgrims', 'umra_trip_id',
        'condition' => 'full_payment_date_time IS NOT NULL'
      ], 'umraTripDiscounts' => [
        self::HAS_MANY, 'UmraTripDiscounts', 'umra_trip_id'
      ],

    ];
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return [
      'id' => 'ID', 'name_english' => 'Name', 'name_dhivehi' => 'ނަން',
      'description_english' => 'Description',
      'description_dhivehi' => 'ތަފްޞީލް', 'month' => 'Month', 'year' => 'Year',
      'departure_date' => 'Departure Date', 'return_date' => 'Return Date',
      'price' => 'Price', 'completed' => 'Completed',
      'deadline_date' => 'Deadline', 'registered' => 'Reg',
      'fullyPaid' => 'Fully<br>paid', 'withBalance' => '> 0<br>Bal.',
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
    $criteria->compare('name_english', $this->name_english, true);
    $criteria->compare('name_dhivehi', $this->name_dhivehi, true);
    $criteria->compare('month', $this->month);
    $criteria->compare('year', $this->year);
    $criteria->compare('price', $this->price);

    return new CActiveDataProvider($this, [
      'criteria' => $criteria,
    ]);
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   *
   * @param string $className active record class name.
   *
   * @return UmraTrips the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public function getTripDate() {
      return (empty($this->departure_date))
        ? ((Yii::app()->language == 'dv'
            ? Helpers::mvMonth($this->month)
          : $this->monthText) . ' ' . $this->year)
        : (Yii::app()->language == 'dv' ? Helpers::mvDate($this->departure_date)
          : (new DateTime($this->departure_date))->format('d F Y'))
        ;
  }

  public function getMonthText() {
    return (new DateTime('2000-' . str_pad($this->month, 2, 0, STR_PAD_LEFT)
      . '-' . '01'))->format('F');
  }

  public function behaviors() {
    return [
      // Classname => path to Class
      'ActiveRecordDateBehavior' => 'application.behaviors.ActiveRecordDateBehavior',
    ];
  }

  public function scopes() {
    return [
      'open' => [
        'condition' => 'completed = 0 && (deadline_date IS NULL ||
			deadline_date > CURDATE())'
      ],
      'closed' => ['condition' => 'completed = 1 || deadline_date < CURDATE()'],
    ];
  }

  public function getClosed() {
    return (new DateTime($this->deadline_date)) < (new DateTime())
    || $this->completed;
  }


}
