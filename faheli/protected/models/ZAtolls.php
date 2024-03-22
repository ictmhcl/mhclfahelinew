<?php

/**
 * This is the model class for table "z_atolls".
 *
 * The followings are the available columns in table 'z_atolls':
 * @property integer $atoll_id
 * @property string $name_english
 * @property string $name_dhivehi
 * @property string $abbreviation_english
 * @property string $abbreviation_dhivehi
 * @property string $atoll_code
 *
 * The followings are the available model relations:
 * @property ZIslands[] $zIslands
 */
class ZAtolls extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'z_atolls';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name_english', 'required'],
			['name_english, name_dhivehi', 'length', 'max'=>250],
			['abbreviation_english, abbreviation_dhivehi, atoll_code', 'length', 'max'=>50],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['atoll_id, name_english, name_dhivehi, abbreviation_english, abbreviation_dhivehi, atoll_code', 'safe', 'on'=>'search'],
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
			'zIslands' => [self::HAS_MANY, 'ZIslands', 'atoll_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'atoll_id' => 'Atoll',
			'name_english' => 'Name English',
			'name_dhivehi' => 'Name Dhivehi',
			'abbreviation_english' => 'Abbreviation English',
			'abbreviation_dhivehi' => 'Abbreviation Dhivehi',
			'atoll_code' => 'Atoll Code',
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

		$criteria->compare('atoll_id',$this->atoll_id);
		$criteria->compare('name_english',$this->name_english,true);
		$criteria->compare('name_dhivehi',$this->name_dhivehi,true);
		$criteria->compare('abbreviation_english',$this->abbreviation_english,true);
		$criteria->compare('abbreviation_dhivehi',$this->abbreviation_dhivehi,true);
		$criteria->compare('atoll_code',$this->atoll_code,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZAtolls the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
