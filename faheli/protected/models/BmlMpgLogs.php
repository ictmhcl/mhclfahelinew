<?php

/**
 * This is the model class for table "bml_mpg_logs".
 *
 * The followings are the available columns in table 'bml_mpg_logs':
 * @property integer $id
 * @property integer $payment_type_id
 * @property integer $person_id
 * @property string $payload
 * @property double $amount
 * @property integer $mpg_response_code
 * @property integer $mpg_reason_code
 * @property string $mpg_reason_description
 * @property string $mpg_signature
 * @property string $mpg_reference_number
 * @property string $mpg_card_number
 * @property string $mpg_authorization_code
 * @property string $issued_date_time
 * @property string $response_date_time
 *
 * The followings are the available model relations:
 * @property Persons $person
 */
class BmlMpgLogs extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bml_mpg_logs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['payment_type_id, person_id, payload, amount, issued_date_time', 'required'],
			['payment_type_id, person_id, mpg_response_code, mpg_reason_code', 'numerical', 'integerOnly'=>true],
			['amount', 'numerical'],
			['response_date_time, mpg_reason_description, mpg_signature, mpg_reference_number, mpg_card_number, mpg_authorization_code', 'safe'],
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
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'payment_type_id' => 'Payment Type',
			'person_id' => 'Person',
			'payload' => 'Payload',
			'amount' => 'Amount',
			'mpg_response_code' => 'Mpg Response Code',
			'mpg_reason_code' => 'Mpg Reason Code',
			'mpg_reason_description' => 'Mpg Reason Description',
			'mpg_signature' => 'Mpg Signature',
			'mpg_reference_number' => 'Mpg Reference Number',
			'mpg_card_number' => 'Mpg Card Number',
			'mpg_authorization_code' => 'Mpg Authorization Code',
			'issued_date_time' => 'Issued Date Time',
			'response_date_time' => 'Response Date Time',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BmlMpgLogs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function behaviors() {
	return [
	'ActiveRecordDateBehavior' =>
	'application.behaviors.ActiveRecordDateBehavior',
	];
	}


}
