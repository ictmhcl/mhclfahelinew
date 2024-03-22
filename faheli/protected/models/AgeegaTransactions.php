<?php

/**
 * This is the model class for table "ageega_transactions".
 *
 * The followings are the available columns in table 'ageega_transactions':
 *
*@property integer $transaction_id
 * @property string $transaction_time
 * @property integer $ageega_id
 * @property string $description_english
 * @property string $description_dhivehi
 * @property integer $transaction_medium_id
 * @property string $amount
 * @property string $balance
 * @property integer $user_id
 * @property integer $is_cancelled
 * @property integer $operation_log_id
 *
 * The followings are the available model relations:
 * @property Ageega $ageega
 * @property ZTransactionMediums $transactionMedium
 * @property Users $user
 * @property string $receiptNo
 * @property string $receiptLink
 * @property string $revisionHistoryLink
 * @property RevisionRequests[] $revisionRequests
 * @property boolean @revised
 */
class AgeegaTransactions extends CActiveRecord
{

	public function getRevised() {
		$approvedRevisions =
				$this->revisionRequests(['condition' => 'confirmed = 1']);

		return !empty($approvedRevisions);
	}


	public function cancelTransaction($reason) {
		if (empty($reason)) {
			throw new CException('A reason is required');
		}
		$this->is_cancelled = 1;
		$dbTrans = Yii::app()->db->beginTransaction();
		$dbTrans->doAudit(ClientAudit::AUDIT_ACTION_DELETE,
				ClientAudit::AUDIT_DATA_PAYMENT_COLLECTION, $this, 'Transaction on ' .
				$this->transaction_time . ' for amount ' .
				Helpers::currency($this->amount) . ' has been cancelled. (Reason: ' .
				$reason . ')');
		$this->save();
		$dbTrans->commit();
	}

	public function reviseTransaction($transaction_time, $transaction_medium_id,
			$reason) {
		// compare only upto the minute
		if ((new DateTime($this->transaction_time))->format('d M Y H:i') ==
				(new DateTime($transaction_time))->format('d M Y H:i') &&
				$this->transaction_medium_id == $transaction_medium_id
		) {
			throw new CException('No change for revision');
		}

		if (empty($reason)) {
			throw new CException('A reason is required');
		}

		$this->transaction_time = $transaction_time;
		$this->transaction_medium_id = $transaction_medium_id;
		$dbTrans = Yii::app()->db->beginTransaction();
		$dbTrans->doAudit(ClientAudit::AUDIT_ACTION_EDIT,
				ClientAudit::AUDIT_DATA_AGEEGA_TRANSACTION, $this, 'Transaction Time
				 or medium updated. (Reason: ' . $reason . ')');

		try {
			$this->save();
			$dbTrans->commit();
		} catch (CException $ex) {
			$dbTrans->rollback();
			ErrorLog::exceptionLog($ex);
		}


	}

	public function getReceiptLink($view = true) {
		return Yii::app()->createUrl('ageega/printReceipt',
				['id'=>$this->transaction_id, 'view' => $view]);
	}

	public function getRevisionHistoryLink() {
		return Yii::app()->createUrl('ageega/viewTransactionHistory', [
				'id' => $this->transaction_id
		]);
	}

	public function getReceiptNo() {
		return
				Helpers::ageegaReceiptNumber($this);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ageega_transactions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['transaction_medium_id, amount, balance, user_id', 'required'],
			['transaction_medium_id, user_id, is_cancelled, operation_log_id', 'numerical', 'integerOnly'=>true],
			['description_english, description_dhivehi', 'length', 'max'=>255],
			['amount, balance', 'length', 'max'=>10],
			['transaction_time', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['transaction_id, transaction_time, ageega_id, description_english, description_dhivehi, transaction_medium_id, amount, balance, user_id, is_cancelled, operation_log_id', 'safe', 'on'=>'search'],
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
			'ageega' => [self::BELONGS_TO, 'Ageega', 'ageega_id'],
			'revisionRequests' => [
					self::HAS_MANY, 'RevisionRequests', 'transaction_id'
			],
			'transactionMedium' => [self::BELONGS_TO, 'ZTransactionMediums', 'transaction_medium_id'],
			'user' => [self::BELONGS_TO, 'Users', 'user_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'transaction_id' => 'Transaction',
			'transaction_time' => 'Transaction Time',
			'ageega_id' => 'Ageega',
			'description_english' => 'Description English',
			'description_dhivehi' => 'Description Dhivehi',
			'transaction_medium_id' => 'Transaction Medium',
			'amount' => 'Amount',
			'balance' => 'Balance',
			'user_id' => 'User',
			'is_cancelled' => 'Is Cancelled',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('transaction_id',$this->transaction_id);
		$criteria->compare('transaction_time',$this->transaction_time,true);
		$criteria->compare('ageega_id',$this->ageega_id);
		$criteria->compare('description_english',$this->description_english,true);
		$criteria->compare('description_dhivehi',$this->description_dhivehi,true);
		$criteria->compare('transaction_medium_id',$this->transaction_medium_id);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('is_cancelled',$this->is_cancelled);
		$criteria->compare('operation_log_id',$this->operation_log_id);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AgeegaTransactions the static model class
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
