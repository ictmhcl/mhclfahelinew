<?php

/**
 * This is the model class for table "sds_statement_details".
 *
 * The followings are the available columns in table 'sds_statement_details':
 * @property integer $id
 * @property integer $sds_statement_id
 * @property integer $sds_contribution_id
 * @property double $staff_amount
 * @property double $organization_amount
 *
 * The followings are the available model relations:
 * @property SdsContribution $sdsContribution
 * @property SdsStatement $sdsStatement
 */
class SdsStatementDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sds_statement_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['sds_statement_id, sds_contribution_id, staff_amount, organization_amount', 'required'],
			['sds_statement_id, sds_contribution_id', 'numerical', 'integerOnly'=>true],
			['staff_amount, organization_amount', 'numerical'],
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
			'sdsContribution' => [self::BELONGS_TO, 'SdsContribution', 'sds_contribution_id'],
			'sdsStatement' => [self::BELONGS_TO, 'SdsStatement', 'sds_statement_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'sds_statement_id' => 'Sds Statement',
			'sds_contribution_id' => 'Sds Contribution',
			'staff_amount' => 'Staff Amount',
			'organization_amount' => 'Organization Amount',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SdsStatementDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}




}
