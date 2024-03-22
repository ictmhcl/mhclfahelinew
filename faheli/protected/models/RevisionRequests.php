<?php

/**
 * This is the model class for table "revision_requests".
 *
 * The followings are the available columns in table 'revision_requests':
 * @property integer $id
 * @property integer $transaction_id
 * @property string $t_type
 * @property string $transaction_time
 * @property integer $transaction_medium_id
 * @property boolean $to_be_cancelled
 * @property string $reason
 * @property integer $requested_by
 * @property string $requested_datetime
 * @property integer $confirmed
 * @property string $confirmed_datetime
 * @property integer $confirmed_by
 * @property integer $is_cancelled
 * @property string $cancelled_datetime
 * @property integer $cancelled_by
 * @property integer $operation_log_id
 *
 * The followings are the available model relations:
 * @property Users $cancelledBy
 * @property Users $confirmedBy
 * @property Users $requestedBy
 * @property ZTransactionMediums $transactionMedium
 *
 * @property MemberTransactions $transaction
 * @property integer $originalMedium
 * @property datetime $originalTransactionTime
 */
class RevisionRequests extends CActiveRecord
{

	public function disapprove() {
		$this->is_cancelled = 1;
		$this->cancelled_by = Yii::app()->user->id;
		$this->cancelled_datetime = Yii::app()->params['dateTime'];
		return $this->save();
	}

	public function approve() {
		if ($this->to_be_cancelled) {
			$this->transaction->cancelTransaction($this->reason);
		} else {
			$this->transaction->reviseTransaction($this->transaction_time,
					$this->transaction_medium_id, $this->reason .
					' (by user '. $this->requestedBy->user_name. ')');
		}
		$this->confirmed = 1;
		$this->confirmed_by = Yii::app()->user->id;
		$this->confirmed_datetime = Yii::app()->params['dateTime'];
		return $this->save();
	}
	/**
	 * @return MemberTransactions|null
	 */
	public function getTransaction() {
		if ($this->isNewRecord)
			return null;
		try {
		$modelName =
				$this->t_type=='1h'? 'MemberTransactions':(
				$this->t_type=='2u'? 'UmraTransactions':(
				$this->t_type=='3n'? 'NonMemberTransactions':'AgeegaTransactions'));
			return $modelName::model()->findByPk($this->transaction_id);
		} catch (CException $ex) {
			ErrorLog::exceptionLog($ex);
			return null;
		}

	}

	/**
	 * @return datetime|null
	 */
	public function getOriginalTransactionTime() {
		try {
			return $this->getTransaction()->transaction_time;
		} catch (CException $ex) {
			ErrorLog::exceptionLog($ex);
			return null;
		}
	}

	/**
	 * @return integer|null
	 */
	public function getOriginalMedium() {
		try {
			return $this->getTransaction()->transaction_medium_id;
		} catch (CException $ex) {
			ErrorLog::exceptionLog($ex);
			return null;
		}
	}



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'revision_requests';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['transaction_id, t_type, transaction_time, transaction_medium_id, reason, requested_by, requested_datetime', 'required'],
			['transaction_id, transaction_medium_id, requested_by, confirmed, confirmed_by, is_cancelled, cancelled_by, operation_log_id', 'numerical', 'integerOnly'=>true],
			['t_type', 'length', 'max'=>4],
			['reason', 'length', 'max'=>255],
			['confirmed_datetime, cancelled_datetime', 'safe'],
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
			'cancelledBy' => [self::BELONGS_TO, 'Users', 'cancelled_by'],
			'confirmedBy' => [self::BELONGS_TO, 'Users', 'confirmed_by'],
			'requestedBy' => [self::BELONGS_TO, 'Users', 'requested_by'],
			'transactionMedium' => [self::BELONGS_TO, 'ZTransactionMediums', 'transaction_medium_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'transaction_id' => 'Transaction',
			't_type' => 'T Type',
			'transaction_time' => 'Revise Time',
			'transaction_medium_id' => 'Revise Medium',
			'to_be_cancelled' => 'To Cancel',
			'reason' => 'Reason',
			'requested_by' => 'Requested By',
			'requested_datetime' => 'Requested Datetime',
			'confirmed' => 'Confirmed',
			'confirmed_datetime' => 'Confirmed Datetime',
			'confirmed_by' => 'Confirmed By',
			'is_cancelled' => 'Is Cancelled',
			'cancelled_datetime' => 'Cancelled Datetime',
			'cancelled_by' => 'Cancelled By',
			'operation_log_id' => 'Operation Log',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RevisionRequests the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function behaviors() {
		return [
				'ActiveRecordDateBehavior' => 'application.behaviors.ActiveRecordDateBehavior',
		];
	}


}
