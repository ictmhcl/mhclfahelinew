<?php

/**
 * This is the model class for table "sds_contribution".
 *
 * The followings are the available columns in table 'sds_contribution':
 *
 * @property integer $id
 * @property integer $organization_staff_id
 * @property integer $member_id
 * @property string $join_date
 * @property string $end_date
 * @property double $staff_amount
 * @property double $staff_temp_amount
 * @property double $organization_amount
 *
 * The followings are the available model relations:
 * @property Members $member
 * @property OrganizationStaff $organizationStaff
 * @property SdsStatementDetails[] $sdsStatementDetails
 */
class SdsContribution extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sds_contribution';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['organization_staff_id, member_id, join_date', 'required'],
			['organization_staff_id, member_id', 'numerical', 'integerOnly'=>true],
			['staff_amount, staff_temp_amount, organization_amount', 'numerical'],
			['end_date', 'safe'],
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
			'member' => [self::BELONGS_TO, 'Members', 'member_id'],
			'organizationStaff' => [self::BELONGS_TO, 'OrganizationStaff', 'organization_staff_id'],
			'sdsStatementDetails' => [
					self::HAS_MANY, 'SdsStatementDetails', 'sds_contribution_id']
			];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'organization_staff_id' => 'Organization Staff',
			'member_id' => 'Member',
			'join_date' => 'Join Date',
			'end_date' => 'End Date',
			'staff_amount' => 'Staff Amount',
			'staff_temp_amount' => 'Next Month Staff Amount',
			'organization_amount' => 'Organization Amount',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SdsContribution the static model class
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
