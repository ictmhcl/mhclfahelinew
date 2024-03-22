<?php

/**
 * This is the model class for table "names_list".
 *
 * The followings are the available columns in table 'names_list':
 * @property integer $id
 * @property string $name_english
 * @property string $name_dhivehi
 * @property integer $occurance
 * @property integer $operation_log_id
 */
class NamesList extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'names_list';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name_english, name_dhivehi, occurance', 'required'],
			['occurance, operation_log_id', 'numerical', 'integerOnly'=>true],
			['name_english, name_dhivehi', 'length', 'max'=>255],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, name_english, name_dhivehi, occurance, operation_log_id', 'safe', 'on'=>'search'],
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
			'occurance' => 'Occurance',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('name_english',$this->name_english,true);
		$criteria->compare('name_dhivehi',$this->name_dhivehi,true);
		$criteria->compare('occurance',$this->occurance);
		$criteria->compare('operation_log_id',$this->operation_log_id);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NamesList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
