<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $user_name
 * @property string $user_secret
 * @property integer $person_id
 * @property integer $organization_id
 * @property integer $branch_id
 * @property string $mobile_number
 * @property string $email
 * @property string $last_login_datetime
 * @property integer $agreed
 * @property integer $user_state_id
 * @property integer $login_code
 * @property integer $code_expiry_time
 * @property integer $operation_log_id
 *
 * The followings are the available model relations:
 * @property AgeegaTransactions[] $ageegaTransactions
 * @property MemberTransactions[] $memberTransactions
 * @property NonMhclMemberTransactions[] $nonMhclMemberTransactions
 * @property OrganizationAdmins[] $organizationAdmins
 * @property UmraTransactions[] $umraTransactions
 * @property UserJobs[] $approvedJobs
 * @property UserJobs[] $requestedJobs
 * @property UserJobs[] $userJobs
 * @property AppFunctions[] $appFunctions
 * @property Organizations $organization
 * @property Branches $branch
 * @property Persons $person
 * @property ZUserStates $userState
 * @property boolean $isSdsUser
 * @property UserJobs $sdsJob
 */
class Users extends CActiveRecord
{

	public function afterSave() {
		if ($this->isNewRecord) {
//			mail($this->email,"New User: " . $this->person->idName .
//					" created","A new user was created","From: admin@mhcl.mv");
		}
		parent::afterSave();
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
      ['user_name, organization_id, mobile_number, email', 'required'],
			['person_id, organization_id, branch_id, agreed, user_state_id,
			login_code, code_expiry_time, operation_log_id', 'numerical', 'integerOnly'=>true],
			['user_name', 'length', 'max'=>20],
			['user_name, email, mobile_number', 'unique'],
			['mobile_number', 'length', 'max'=>7],
      ['mobile_number', 'ext.validators.mobilePhoneNumber'],
			['email', 'length', 'max'=>50],
			['last_login_datetime', 'safe'],
      ['email', 'email'],
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
			'ageegaTransactions' => [self::HAS_MANY, 'AgeegaTransactions', 'user_id'],
			'memberTransactions' => [self::HAS_MANY, 'MemberTransactions', 'user_id'],
			'nonMhclMemberTransactions' => [self::HAS_MANY, 'NonMhclMemberTransactions', 'user_id'],
			'organizationAdmins' => [self::HAS_MANY, 'OrganizationAdmins', 'user_id'],
			'umraTransactions' => [self::HAS_MANY, 'UmraTransactions', 'user_id'],
			'approvedJobs' => [self::HAS_MANY, 'UserJobs', 'approved_user_id'],
			'requestedJobs' => [self::HAS_MANY, 'UserJobs', 'requested_user_id'],
			'userJobs' => [self::HAS_MANY, 'UserJobs', 'user_id'],
			'appFunctions' => [self::MANY_MANY, 'AppFunctions', 'user_permissions(user_id, app_function_id)'],
			'organization' => [self::BELONGS_TO, 'Organizations', 'organization_id'],
      'branch' => [self::BELONGS_TO, 'Branches', 'branch_id'],
			'person' => [self::BELONGS_TO, 'Persons', 'person_id'],
			'userState' => [self::BELONGS_TO, 'ZUserStates', 'user_state_id'],
			'sdsJob' => [self::HAS_ONE, 'UserJobs', 'user_id', 'condition' =>
					'job_id = :sdsJobId and approved_datetime IS NOT NULL and
					cancelled_datetime IS NULL', 'params'	=> [':sdsJobId' => Constants::JOB_SDS_ADMIN]]
		];
	}

	/**
	 * @param $jobId
	 *
	 * @return \UserJobs
	 */
	public function assignJob($jobId) {

		$userJob = new UserJobs();
		$userJob->setAttributes([
			'user_id' => $this->id,
			'job_id' => (int) $jobId,
			'requested_user_id' => Yii::app()->user->id,
			'requested_datetime' => Yii::app()->params['dateTime'],
			'approved_user_id' => Yii::app()->user->id,
			'approved_datetime' => Yii::app()->params['dateTime']
		]);
		$userJob->save();
		return $userJob;
	}

	public function getNameOrg() {
		return $this->person->full_name_english . ' (' .
		$this->organization->membership_prefix . ')';
	}

	public function getIsSdsUser() {
		return !empty($this->sdsJob);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_name' => 'Username',
			'user_secret' => 'User Secret',
			'person_id' => 'Person',
			'organization_id' => 'Organization',
      'branch_id' => 'Branch',
			'mobile_number' => 'Mobile Number',
			'email' => 'Email',
      'last_login_datetime' => 'Last Login',
      'agreed' => 'Agree',
			'user_state_id' => 'User State',
			'login_code' => 'Login Code',
			'operation_log_id' => 'Operation Log',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
   *
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
