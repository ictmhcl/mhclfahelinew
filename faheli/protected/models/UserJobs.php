<?php

/**
 * This is the model class for table "user_jobs".
 *
 * The followings are the available columns in table 'user_jobs':
 * @property integer $id
 * @property integer $user_id
 * @property integer $job_id
 * @property integer $requested_user_id
 * @property string $requested_datetime
 * @property integer $approved_user_id
 * @property string $approved_datetime
 * @property integer $cancelled_user_id
 * @property string $cancelled_datetime
 *
 * The followings are the available model relations:
 * @property Users $approvedUser
 * @property Jobs $job
 * @property Users $requestedUser
 * @property Users $user
 */
class UserJobs extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_jobs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['user_id, job_id, requested_user_id, requested_datetime', 'required'],
			['user_id, job_id, requested_user_id, approved_user_id', 'numerical', 'integerOnly'=>true],
			['job_id', 'existingJob'],
			['approved_datetime', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, user_id, job_id, requested_user_id, requested_datetime, approved_user_id, approved_datetime', 'safe', 'on'=>'search'],
		];
	}

	public function existingJob() {
		$criteria = new CDbCriteria();
		if (!$this->isNewRecord) {
			$criteria->condition = 'id != :id';
			$criteria->params = [':id' => $this->id];
		}
		$criteria->addCondition('approved_datetime IS NOT NULL');
		$criteria->addColumnCondition([
			'user_id' => $this->user_id,
			'job_id' => $this->job_id,
			'cancelled_datetime' => NULL
		]);
		if (!empty(UserJobs::model()->find($criteria)))
			$this->addError('base', 'The requested Job is already assigned to the
			user');
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'approvedUser' => [self::BELONGS_TO, 'Users', 'approved_user_id'],
			'job' => [self::BELONGS_TO, 'Jobs', 'job_id'],
			'requestedUser' => [self::BELONGS_TO, 'Users', 'requested_user_id'],
			'user' => [self::BELONGS_TO, 'Users', 'user_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_id' => 'User',
			'job_id' => 'Job',
			'requested_user_id' => 'Requested User',
			'requested_datetime' => 'Requested Datetime',
			'approved_user_id' => 'Approved User',
			'approved_datetime' => 'Approved Datetime',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('job_id',$this->job_id);
		$criteria->compare('requested_user_id',$this->requested_user_id);
		$criteria->compare('requested_datetime',$this->requested_datetime,true);
		$criteria->compare('approved_user_id',$this->approved_user_id);
		$criteria->compare('approved_datetime',$this->approved_datetime,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	public function approve() {
		$this->approved_datetime = Yii::app()->params['dateTime'];
		$this->approved_user_id = Yii::app()->user->id;
		$this->save();
	}

	public function cancel() {
		$this->cancelled_datetime = Yii::app()->params['dateTime'];
		$this->cancelled_user_id = Yii::app()->user->id;
		$this->save();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserJobs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
