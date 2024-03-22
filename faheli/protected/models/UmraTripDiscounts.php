<?php

/**
 * This is the model class for table "umra_trip_discounts".
 *
 * The followings are the available columns in table 'umra_trip_discounts':
 * @property integer $id
 * @property integer $umra_trip_id
 * @property string $name_english
 * @property string $name_dhivehi
 * @property string $description
 * @property double $discount_amount
 * @property integer $operation_log_id
 *
 * The followings are the available model relations:
 * @property UmraPilgrims[] $umraPilgrims
 * @property UmraTrips $umraTrip
 */
class UmraTripDiscounts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'umra_trip_discounts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['umra_trip_id, name_english, name_dhivehi, description, discount_amount', 'required'],
			['umra_trip_id, operation_log_id', 'numerical', 'integerOnly'=>true],
			['discount_amount', 'numerical'],
			['name_english, name_dhivehi, description', 'length', 'max'=>255],
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
			'umraPilgrims' => [self::HAS_MANY, 'UmraPilgrims', 'umra_trip_discount_id'],
			'umraTrip' => [self::BELONGS_TO, 'UmraTrips', 'umra_trip_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'umra_trip_id' => 'Umra Trip',
			'name_english' => 'Name',
			'name_dhivehi' => 'ނަން',
			'description' => 'Description',
			'discount_amount' => 'Discount (Rf)',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UmraTripDiscounts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}




}
