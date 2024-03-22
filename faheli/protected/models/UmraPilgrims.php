<?php



/**
 * This is the model class for table "umra_pilgrims".
 *
 * The followings are the available columns in table 'umra_pilgrims':
 *
*@property integer $id
 * @property integer $umra_pilgrim_no
 * @property integer $person_id
 * @property integer $umra_trip_id
 * @property integer $current_island_id
 * @property string $current_address_english
 * @property string $current_address_dhivehi
 * @property string $phone_number
 * @property string $email_address
 * @property integer $mahram_id
 * @property string $group_name
 * @property string $application_form
 * @property string $id_copy
 * @property string $mahram_document
 * @property double $account_balance
 * @property string $full_payment_date_time
 *
 * The followings are the available model relations:
 * @property UmraPilgrims $mahram
 * @property Persons $person
 * @property UmraTrips $umraTrip
 * @property UmraTripDiscounts $umraTripDiscount
 * @property UmraTransactions[] $umraTransactions
 * @property UmraTransactions   $lastPayment
 * @property double $totalPaid
 * @property double $discountAmount
 * @property double $due
 */
class UmraPilgrims extends CActiveRecord
{
	public function afterFind() {
		parent::afterFind();
		$this->account_balance = $this->totalPaid;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'umra_pilgrims';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['umra_trip_id, umra_pilgrim_no, current_island_id,
			current_address_english, current_address_dhivehi, phone_number,
			ec_address_english, ec_address_dhivehi, ec_phone_number, ec_id_no', 
			 'required', 'message' => '{attribute} ' . H::t('site','required')],
			// ['application_form', 'required', 'message' => '{attribute} ' . H::t
					// ('site','attach')],
			['person_id', 'duplicateCheck'],
			// ['mahram_id, mahram_document', 'mahramCheck'],
			// ['mahram_id, mahram_document', 'required', 'on' => 'display'],
			['person_id, umra_trip_id, current_island_id', 'numerical', 'integerOnly'=>true],
			['account_balance', 'numerical'],
			['current_address_english, current_address_dhivehi, group_name, application_form, id_copy, mahram_document, emergency_contact_full_name_dhivehi, emergency_contact_full_name_english', 'length', 'max'=>255],
			['email_address', 'email', 'message' => '{attribute}' . H::t('site',
							'isWrong')],
			['phone_number', 'length', 'max'=>25],
			['full_payment_date_time', 'safe'],
		];
	}

	public function mahramCheck($attribute) {
		if (empty($this->person_id) || empty($this->umra_trip_id) ||
				($this->person->gender_id != Constants::GENDER_FEMALE))
			return;
		$depDate = empty($this->umraTrip->departure_date)?
				($this->umraTrip->year . "-" . str_pad($this->umraTrip->month,2,'0',
								STR_PAD_LEFT) . "-01"):
				$this->umraTrip->departure_date;
		if ($this->person->gender_id == Constants::GENDER_FEMALE &&
				Helpers::age($this->person->d_o_b, $depDate) < Helpers::config
				('noMahramAge')) {
			if (empty($this->$attribute)) {
				$this->addError($attribute,$this->getAttributeLabel($attribute) . ($attribute == 'mahram_id'
								? H::t('site','required') : H::t('site','attach')));
			} else {
				if ($attribute == 'mahram_id') {
					if (preg_match(Constants::ID_CARD_PATTERN, $this->$attribute)) {
            /** @var Persons $mahramPerson */
            $mahramPerson = Persons::model()
              ->findByAttributes(['id_no' => $this->$attribute]);
            if (empty($mahramPerson)) {
              $this->addError($attribute, H::t('umra', 'mahramIdError'));
              return;
            }
            if ($mahramPerson->gender_id != Constants::GENDER_MALE) {
              $this->addError($attribute, H::t('umra', 'mahramMaleRequired'));

              return;

            }
            /** @var UmraPilgrims $mahramPilgrim */
            $mahramPilgrim = UmraPilgrims::model()->findbyAttributes([
              'person_id' => $mahramPerson->id,
              'umra_trip_id' => $this->umra_trip_id
            ]);
            if (empty($mahramPilgrim)) {
              $this->addError($attribute, 'މަޙްރަމް އަދި މި ޢުމްރާ ދަތުރުގައި
               ރަޖިސްޓަރ ކުރެވިފައެއް ނެތް. ފުރަތަމަ މަޙްރަމް ރަޖިސްޓަރ
               ކުރައްވާ');

              return;
            }
            $this->$attribute = $mahramPilgrim->id;
          } else {
            if (!ctype_digit($this->$attribute))
            {
              $this->addError($attribute, 'މަޙްރަމް އައި.ޑީ ނަންބަރު
              ރަނގަޅެއް ނޫން.');
              return;
            }
            else {
              /** @var UmraPilgrims $mahramPilgrim */
              $mahramPilgrim = UmraPilgrims::model()->findByAttributes([
                'id' => $this->$attribute,
                'umra_trip_id' => $this->umra_trip_id
              ]);
              if (empty($mahramPilgrim))
              {
                $this->addError($attribute, 'މަޙްރަމް އެއް ރަޖިސްޓަރ
              ކުރެވިފައެއް ނެތް. ފުރަތަމަ މަޙްރަމް މިޢުމްރާއަށް ރަޖިސްޓަރ
              ކުރައްވާ');
                return;
              }
              else if ($mahramPilgrim->person->gender_id ==
                Constants::GENDER_FEMALE) {
                $this->addError($attribute, 'މަޙްރަމެއްގެ ގޮތުގައި
                ޖައްސަވަންވާނީ މިދަތުރުގައި ރަޖިސްޓްރީ ކުރެވިފައިވާ
                ފިރިހެނެކެވެ.');
                return;
              }
            }
					}
				}
			}
		}
	}

	public function getDue() {
		return $this->umraTrip->price - $this->totalPaid - $this->discountAmount;
	}

	public function getDiscountAmount() {
		if (!empty($this->umraTripDiscount))
			return $this->umraTripDiscount->discount_amount;
		return 0;
	}

	/**
	 * Checks if the individual is already registered for the selected Umra Trip
	 * @param $attribute
	 */
	public function duplicateCheck($attribute) {
		if (!$this->isNewRecord)
			return;
		$existingPilgrim = UmraPilgrims::model()->findByAttributes([
				'person_id' => $this->$attribute,
				'umra_trip_id' => $this->umra_trip_id
		]);

		if ($existingPilgrim !== null) {
			$this->addError($attribute, 'This individual has already registered for
			 the selected Umra Trip');
		}

	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'mahram' => [self::BELONGS_TO, 'UmraPilgrims', 'mahram_id'],
			'person' => [self::BELONGS_TO, 'Persons', 'person_id'],
			'mahramFor' => [self::HAS_MANY, 'UmraPilgrims', 'mahram_id'],
			'umraTrip' => [self::BELONGS_TO, 'UmraTrips', 'umra_trip_id'],
			'umraTripDiscount' => [self::BELONGS_TO, 'UmraTripDiscounts', 'umra_trip_discount_id'],
			'umraTransactions' => [self::HAS_MANY, 'UmraTransactions', 'umra_pilgrim_id'],
			'lastPayment' => [
					self::HAS_ONE, 'UmraTransactions', 'umra_pilgrim_id',
					'condition' => 'amount > 0', 'order' => 'transaction_time desc'],
			'totalPaid' => [self::STAT, 'UmraTransactions', 'umra_pilgrim_id',
			'select' => 'sum(amount)', 'condition' => 'is_cancelled = 0']
			];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'person_id' => 'Person',
			'umra_trip_id' => 'Umra Trip',
			'current_island_id' => H::t('umra', 'current_island_id'),
			'current_address_english' => H::t('umra', 'current_address_english'),
			'current_address_dhivehi' => H::t('umra', 'current_address_dhivehi'),
			'phone_number' => H::t('site', 'mobile'),
			'email_address' => H::t('site', 'email_address'),
			'mahram_id' => H::t('umra', 'mahramId'),
			'group_name' => H::t('hajj', 'group_name'),
			'application_form' => H::t('umra','application_form'),
			'id_copy' => 'ID Card',
			'mahram_document' => H::t('hajj', 'mahram_document'),
			'account_balance' => 'Account Balance',
			'full_payment_date_time' => 'Full Payment Date',
			
			'ec_address_english' => H::t('umra', 'ec_address_english'),
			'ec_address_dhivehi' => H::t('umra', 'ec_address_dhivehi'),
			'ec_phone_number' => H::t('site', 'mobile'),
			'ec_full_name' => H::t('umra', 'ec_full_name'),
			'id_no' => H::t('site', 'id_no'),
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('person_id',$this->person_id);
		$criteria->compare('umra_trip_id',$this->umra_trip_id);
		$criteria->compare('current_island_id',$this->current_island_id);
		$criteria->compare('current_address_english',$this->current_address_english,true);
		$criteria->compare('current_address_dhivehi',$this->current_address_dhivehi,true);
		$criteria->compare('phone_number',$this->phone_number,true);
		$criteria->compare('email_address',$this->email_address,true);
		$criteria->compare('mahram_id',$this->mahram_id);
		$criteria->compare('group_name',$this->group_name,true);
		$criteria->compare('application_form',$this->application_form,true);
		$criteria->compare('id_copy',$this->id_copy,true);
		$criteria->compare('mahram_document',$this->mahram_document,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	public function getPendingAmount() {
		return $this->umraTrip->price - $this->account_balance
			- (!empty($this->umraTripDiscount)
				?$this->umraTripDiscount->discount_amount:0);
	}

	public function getUMRA_PILGRIM_ID() {
		$prefix = 'U';
		$year = substr(trim($this->umraTrip->year),2,2);
		$month = str_pad(trim($this->umraTrip->month),2,0,STR_PAD_LEFT);
		return $prefix . $year . $month . '-' . str_pad($this->umra_pilgrim_no,3,0,STR_PAD_LEFT);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UmraPilgrims the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function behaviors() {
		return [
			// Classname => path to Class
				'ActiveRecordDateBehavior' =>
						'application.behaviors.ActiveRecordDateBehavior',
		];
	}

}
