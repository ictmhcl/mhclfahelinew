<?php

/**
 * This is the model class for table "training_location_members".
 *
 * The followings are the available columns in table 'training_location_members':
 * @property integer $id
 * @property integer $training_location_id
 * @property integer $hajj_list_member_id
 *
 * The followings are the available model relations:
 * @property TrainingLocations $trainingLocation
 * @property HajjListMembers $hajjListMember
 */
class TrainingLocationMembers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'training_location_members';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['training_location_id, hajj_list_member_id', 'required'],
			['training_location_id, hajj_list_member_id', 'numerical', 'integerOnly'=>true],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, training_location_id, hajj_list_member_id', 'safe', 'on'=>'search'],
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
			'trainingLocation' => [self::BELONGS_TO, 'TrainingLocations', 'training_location_id'],
			'hajjListMember' => [self::BELONGS_TO, 'HajjListMembers', 'hajj_list_member_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'training_location_id' => 'Location',
			'hajj_list_member_id' => 'Member',
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
		$criteria->compare('training_location_id',$this->training_location_id);
		$criteria->compare('hajj_list_member_id',$this->hajj_list_member_id);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TrainingLocationMembers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
