<?php

/**
 * This is the model class for table "sds_statement".
 *
 * The followings are the available columns in table 'sds_statement':
 *
*@property integer $id
 * @property integer $organization_id
 * @property string $statement_date
 * @property string $deposit_datetime
 * @property string $statement_month
 * @property integer $user_id
 * @property double $deposit_amount
 * @property string $deposit_copy
 * @property string $deposit_confirmation_datetime
 * @property integer $deposit_confirmation_user_id
 * @property boolean $is_cancelled
 * @property integer $cancelled_user_id
 * @property datetime $cancelled_datetime
 *
 * The followings are the available model relations:
 * @property Users $depositConfirmationUser
 * @property Organizations $organization
 * @property Users $user
 * @property SdsStatementDetails[] $sdsStatementDetails
 * @property integer               $status
 */
class SdsStatement extends CActiveRecord
{
	public function getStatus() {
		if ($this->is_cancelled) {
			return Constants::SDS_STATEMENT_CANCELLED;
		}
		if (empty($this->deposit_confirmation_datetime)) {
			return Constants::SDS_STATEMENT_UNCONFIRMED;
		}

		return Constants::SDS_STATEMENT_CONFIRMED;
	}

	public function getCounts() {
		return Yii::app()->db->createCommand("
			select
				count(distinct(sc.organization_staff_id)) as staffCount,
				count(ssd.id) as memberCount
			from sds_statement_details ssd
			join sds_contribution sc on sc.id = ssd.sds_contribution_id
			where ssd.sds_statement_id = :statementId
			limit 1;
		")->queryAll(true, [':statementId' => $this->id])[0];
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sds_statement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['organization_id, statement_date, deposit_datetime, statement_month, user_id', 'required'],
			['organization_id, user_id, deposit_confirmation_user_id', 'numerical', 'integerOnly'=>true],
			['deposit_copy', 'length', 'max'=>255],
			['deposit_confirmation_datetime', 'safe'],
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
			'depositConfirmationUser' => [self::BELONGS_TO, 'Users', 'deposit_confirmation_user_id'],
			'organization' => [self::BELONGS_TO, 'Organizations', 'organization_id'],
			'user' => [self::BELONGS_TO, 'Users', 'user_id'],
			'sdsStatementDetails' => [self::HAS_MANY, 'SdsStatementDetails', 'sds_statement_id'],
			'operationLog' => [
					self::BELONGS_TO, 'OperationLogs', 'operation_log_id'
			],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'organization_id' => 'Organization',
			'statement_date' => 'Statement Date',
			'deposit_datetime' => 'Deposit Datetime',
			'statement_month' => 'Statement Month',
			'user_id' => 'User',
			'deposit_copy' => 'Deposit Copy',
			'deposit_confirmation_datetime' => 'Deposit Confirmation Datetime',
			'deposit_confirmation_user_id' => 'Deposit Confirmation User',
			'is_cancelled' => 'Is Cancelled', 'cancelled_user_id' => 'Cancelled User',
			'cancelled_datetime' => 'Cancelled DateTime',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SdsStatement the static model class
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
