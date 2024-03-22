<?php

/**
 * This is the model class for table "prescriptions".
 *
 * The followings are the available columns in table 'prescriptions':
 * @property integer $id
 * @property string $id_no
 * @property string $datetime
 * @property string $co
 * @property string $oe
 * @property string $diagnosis
 * @property string $px
 *
 * @property Persons $person
 */
class Prescriptions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'prescriptions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['id_no, datetime', 'required'],
      ['co', 'oneFieldNotEmpty'],
			['id_no', 'length', 'max'=>50],
			['co, oe, diagnosis, px', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, id_no, datetime, co, oe, diagnosis, px', 'safe', 'on'=>'search'],
    ];
	}

  public function oneFieldNotEmpty() {
    if (""==(trim($this->co).trim($this->oe).
      trim($this->diagnosis).trim($this->px))) {
      $this->addError('', 'No data.');
    }
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

  public function getPerson() {
    return Persons::model()->findByAttributes([
      'id_no' => $this->id_no
    ]);
  }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'id_no' => 'Id No',
			'datetime' => 'Datetime',
			'co' => 'C/O',
			'oe' => 'O/E',
			'diagnosis' => 'Dx',
			'px' => 'Rx',
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
		$criteria->compare('id_no',$this->id_no,true);
		$criteria->compare('datetime',$this->datetime,true);
		$criteria->compare('co',$this->co,true);
		$criteria->compare('oe',$this->oe,true);
		$criteria->compare('diagnosis',$this->diagnosis,true);
		$criteria->compare('px',$this->px,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Prescriptions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
