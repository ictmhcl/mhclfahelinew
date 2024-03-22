<?php

/**
 * This is the model class for table "ageega_children".
 *
 * The followings are the available columns in table 'ageega_children':
 * @property integer $id
 * @property integer $ageega_id
 * @property string $full_name_dhivehi
 * @property string $full_name_arabic
 * @property string $full_name_english
 * @property integer $gender_id
 * @property string $birth_certificate_no
 * @property integer $sheep_qty
 *
 * The followings are the available model relations:
 * @property Ageega $ageega
 * @property ZGender $gender
 */
class AgeegaChildren extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ageega_children';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['full_name_dhivehi, gender_id, birth_certificate_no, sheep_qty', 'required'],
			['gender_id, sheep_qty', 'numerical', 'integerOnly'=>true],
			['full_name_dhivehi, full_name_arabic, full_name_english', 'length', 'max'=>255],
			['birth_certificate_no', 'length', 'max'=>100],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, ageega_id, full_name_dhivehi, full_name_arabic, full_name_english, gender_id, birth_certificate_no, sheep_qty', 'safe', 'on'=>'search'],
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
			'ageega' => [self::BELONGS_TO, 'Ageega', 'ageega_id'],
			'gender' => [self::BELONGS_TO, 'ZGender', 'gender_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'ageega_id' => 'Ageega Principal',
			'full_name_dhivehi' => 'ފުރިހަމަ ނަން',
			'full_name_arabic' => 'ޢަރަބި ނަން',
			'full_name_english' => 'Name',
			'gender_id' => 'Gender',
			'birth_certificate_no' => 'Birth Certificate Number',
			'sheep_qty' => 'Number of Sheep',
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
		$criteria->compare('ageega_id',$this->ageega_id);
		$criteria->compare('full_name_dhivehi',$this->full_name_dhivehi,true);
		$criteria->compare('full_name_arabic',$this->full_name_arabic,true);
		$criteria->compare('full_name_english',$this->full_name_english,true);
		$criteria->compare('gender_id',$this->gender_id);
		$criteria->compare('birth_certificate_no',$this->birth_certificate_no,true);
		$criteria->compare('sheep_qty',$this->sheep_qty);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AgeegaChildren the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
