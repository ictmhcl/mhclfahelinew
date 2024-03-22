<?php

/**
 * This is the model class for table "z_country".
 *
 * The followings are the available columns in table 'z_country':
 * @property string $id
 * @property string $name
 * @property string $name_dhivehi
 * @property string $iso_alpha2
 * @property string $iso_alpha3
 * @property integer $iso_numeric
 * @property string $currency_code
 * @property string $currency_name
 * @property string $currrency_symbol
 * @property string $flag
 *
 * The followings are the available model relations:
 * @property Registrations[] $registrations
 * @property Persons[] $persons
 */
class ZCountry extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'z_country';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['iso_numeric', 'numerical', 'integerOnly'=>true],
			['name, name_dhivehi', 'length', 'max'=>200],
			['iso_alpha2', 'length', 'max'=>2],
			['iso_alpha3, currency_code, currrency_symbol', 'length', 'max'=>3],
			['currency_name', 'length', 'max'=>32],
			['flag', 'length', 'max'=>6],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, name, name_dhivehi, iso_alpha2, iso_alpha3, iso_numeric, currency_code, currency_name, currrency_symbol, flag', 'safe', 'on'=>'search'],
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
				'persons' => [self::HAS_MANY, 'Persons', 'country_id'],
				'registrations' => [self::HAS_MANY, 'Registrations', 'country_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Name',
			'name_dhivehi' => 'ނަން',
			'iso_alpha2' => 'Iso Alpha2',
			'iso_alpha3' => 'Iso Alpha3',
			'iso_numeric' => 'Iso Numeric',
			'currency_code' => 'Currency Code',
			'currency_name' => 'Currency Name',
			'currrency_symbol' => 'Currrency Symbol',
			'flag' => 'Flag',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_dhivehi',$this->name_dhivehi,true);
		$criteria->compare('iso_alpha2',$this->iso_alpha2,true);
		$criteria->compare('iso_alpha3',$this->iso_alpha3,true);
		$criteria->compare('iso_numeric',$this->iso_numeric);
		$criteria->compare('currency_code',$this->currency_code,true);
		$criteria->compare('currency_name',$this->currency_name,true);
		$criteria->compare('currrency_symbol',$this->currrency_symbol,true);
		$criteria->compare('flag',$this->flag,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZCountry the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
