<?php

/**
 * This is the model class for table "ramadan_umra_1437_fahu_15".
 *
 * The followings are the available columns in table 'ramadan_umra_1437_fahu_15':
 * @property integer $id
 * @property integer $hajji_no
 * @property string $id_no
 * @property string $pp_no
 * @property string $full_name_english
 * @property string $full_name_arabic
 * @property string $full_name_dhivehi
 * @property string $Bus
 * @property string $Room
 * @property string $Flight
 * @property Passports $passport
 */
class RamadanUmra1437Fahu15 extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ramadan_umra_1437_fahu_15';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['hajji_no', 'numerical', 'integerOnly'=>true],
			['id_no, pp_no, full_name_english, full_name_arabic, full_name_dhivehi', 'length', 'max'=>255],
			['Bus, Room, Flight', 'length', 'max'=>20],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, hajji_no, id_no, pp_no, full_name_english, full_name_arabic, full_name_dhivehi, Bus, Room, Flight', 'safe', 'on'=>'search'],
		];
	}

	public function getPassport() {
		return Passports::model()->findByAttributes([
				'id_no' => $this->id_no
		]);
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
			'hajji_no' => 'Hajji No',
			'id_no' => 'Id No',
			'pp_no' => 'Pp No',
			'full_name_english' => 'Full Name English',
			'full_name_arabic' => 'Full Name Arabic',
			'full_name_dhivehi' => 'Full Name Dhivehi',
			'Bus' => 'Bus',
			'Room' => 'Room',
			'Flight' => 'Flight',
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
		$criteria->compare('hajji_no',$this->hajji_no);
		$criteria->compare('id_no',$this->id_no,true);
		$criteria->compare('pp_no',$this->pp_no,true);
		$criteria->compare('full_name_english',$this->full_name_english,true);
		$criteria->compare('full_name_arabic',$this->full_name_arabic,true);
		$criteria->compare('full_name_dhivehi',$this->full_name_dhivehi,true);
		$criteria->compare('Bus',$this->Bus,true);
		$criteria->compare('Room',$this->Room,true);
		$criteria->compare('Flight',$this->Flight,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RamadanUmra1437Fahu15 the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
