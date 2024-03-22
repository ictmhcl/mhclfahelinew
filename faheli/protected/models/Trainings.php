<?php

/**
 * This is the model class for table "trainings".
 *
 * The followings are the available columns in table 'trainings':
 * @property integer $id
 * @property integer $hajj_id
 * @property string $training_name_english
 * @property string $training_name_dhivehi
 *
 * The followings are the available model relations:
 * @property TrainingLocations[] $trainingLocations
 * @property Hajjs $hajj
 */
class Trainings extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'trainings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['hajj_id, training_name_english', 'required'],
			['hajj_id', 'numerical', 'integerOnly'=>true],
			['training_name_english, training_name_dhivehi', 'length', 'max'=>255],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, hajj_id, training_name_english, training_name_dhivehi', 'safe', 'on'=>'search'],
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
			'trainingLocations' => [self::HAS_MANY, 'TrainingLocations', 'training_id'],
			'hajj' => [self::BELONGS_TO, 'Hajjs', 'hajj_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'hajj_id' => 'Hajj',
			'training_name_english' => 'Training Name',
			'training_name_dhivehi' => 'ނަން',
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
		$criteria->compare('hajj_id',$this->hajj_id);
		$criteria->compare('training_name_english',$this->training_name_english,true);
		$criteria->compare('training_name_dhivehi',$this->training_name_dhivehi,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Trainings the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
