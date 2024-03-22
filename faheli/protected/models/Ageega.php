<?php

/**
 * This is the model class for table "ageega".
 *
 * The followings are the available columns in table 'ageega':
 *
 * @property integer $id
 * @property integer $person_id
 * @property string $phone_number
 * @property string $full_payment_date_time
 * @property integer $ageega_reason_id
 * @property double $paid_amount
 * @property string $ageega_form
 *
 * The followings are the available model relations:
 * @property Persons $person
 * @property ZAgeegaReasons $ageegaReason
 * @property AgeegaChildren[] $ageegaChildrens
 * @property AgeegaTransactions[] $ageegaTransactions
 * @property double $totalPaid
 * @property integer $sheepCount
 * @property bool $isCancelled
 */
class Ageega extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ageega';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['phone_number, paid_amount', 'required', 'message' => '{attribute} ' .
					H::t('site','required')],
			['ageega_form', 'required', 'message' => '{attribute} ' . H::t('site',
							'attach')],
			['person_id, ageega_reason_id', 'numerical', 'integerOnly'=>true],
			['paid_amount', 'numerical'],
			['phone_number, ageega_form', 'length', 'max'=>255],
			['full_payment_date_time', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, person_id, phone_number, full_payment_date_time, paid_amount, ageega_form', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'person' => [self::BELONGS_TO, 'Persons', 'person_id'],
			'ageegaReason' => [self::BELONGS_TO, 'ZAgeegaReasons', 'ageega_reason_id'],
			'ageegaChildrens' => [self::HAS_MANY, 'AgeegaChildren', 'ageega_id'],
			'ageegaTransactions' => [self::HAS_MANY, 'AgeegaTransactions', 'ageega_id'],
			'totalPaid' => [self::STAT, 'AgeegaTransactions', 'ageega_id',
			'select' => 'sum(amount)', 'condition' => 'is_cancelled = 0'],
			'sheepCount' => [self::STAT, 'AgeegaChildren', 'ageega_id',
			'select' => 'sum(sheep_qty)'],
			'childrenNames' => [self::STAT, 'AgeegaChildren', 'ageega_id',
					'select' => 'group_concat('.H::tf('full_name_dhivehi').' separator
					"'.(Yii::app()->language=='dv'?'، ':', ').'")',
					'condition' => 'full_name_dhivehi <> \'-\'']
		];
	}

	public function getIsCancelled() {
		return empty($this->totalPaid);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'person_id' => 'Person',
			'phone_number' => 'Phone',
			'full_payment_date_time' => 'Full Payment Date Time',
			'reason' => H::t('ageega', 'ageegaType'),
			'paid_amount' => 'Paid Amount',
			'ageega_form' => $this->ageega_reason_id ==
			Constants::AGEEGA_REASON_CHILDREN_NAMING? H::t('ageega',
					'formDocLabelWithChildren'):H::t('ageega','formDocLabel'),
		];
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Ageega the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
