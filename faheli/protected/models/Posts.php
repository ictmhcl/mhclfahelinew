<?php

/**
 * This is the model class for table "posts".
 *
 * The followings are the available columns in table 'posts':
 * @property integer $id
 * @property string $name_english
 * @property string $name_dhivehi
 * @property integer $employment_level_id
 * @property string $created_date
 * @property integer $report_to_post_id
 * @property string $end_date
 * @property integer $board_resolution_id
 * @property string $job_description
 * @property integer $work_location_id
 * @property integer $hiring_lead
 *
 * The followings are the available model relations:
 * @property Employees[] $employees
 * @property Posts $reportToPost
 * @property Posts[] $posts
 */
class Posts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'posts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name_english, created_date', 'required'],
			['employment_level_id, report_to_post_id, board_resolution_id, work_location_id, hiring_lead', 'numerical', 'integerOnly'=>true],
			['name_english, name_dhivehi', 'length', 'max'=>255],
			['end_date, job_description', 'safe'],
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
			'employees' => [self::HAS_MANY, 'Employees', 'post_id'],
			'reportToPost' => [self::BELONGS_TO, 'Posts', 'report_to_post_id'],
			'posts' => [self::HAS_MANY, 'Posts', 'report_to_post_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name_english' => 'Name English',
			'name_dhivehi' => 'Name Dhivehi',
			'employment_level_id' => 'Employment Level',
			'created_date' => 'Created Date',
			'report_to_post_id' => 'Report To Post',
			'end_date' => 'End Date',
			'board_resolution_id' => 'Board Resolution',
			'job_description' => 'Job Description',
			'work_location_id' => 'Work Location',
			'hiring_lead' => 'Hiring Lead',
		];
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->db_attendance;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Posts the static model class
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
