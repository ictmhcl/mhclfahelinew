<?php

/**
 * This is the model class for table "routes".
 *
 * The followings are the available columns in table 'routes':
 * @property integer $id
 * @property integer $trip_list_id
 * @property string $name_english
 * @property string $name_dhivehi
 *
 * The followings are the available model relations:
 * @property Buses[] $buses
 * @property Flights[] $flights
 * @property TripLists $tripList
 *
 * @property integer $busCapacity
 * @property integer $bookedBusSeats
 */
class Routes extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'routes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name_english, name_dhivehi', 'required'],
			['trip_list_id', 'numerical', 'integerOnly'=>true],
			['name_english, name_dhivehi', 'length', 'max'=>255],
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
			'buses' => [self::HAS_MANY, 'Buses', 'route_id'],
			'flights' => [self::HAS_MANY, 'Flights', 'route_id'],
			'tripList' => [self::BELONGS_TO, 'TripLists', 'trip_list_id'],
			'busCapacity' => [
					self::STAT, 'buses', 'route_id', 'select' => 'sum(seats)'
			],
		];
	}

	public function getBookedBusSeats() {
		$x = 0;
		foreach ($this->buses as $bus) {
			$x += $bus->taken;
		}

		return $x;
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'trip_list_id' => 'Trip List',
			'name_english' => 'Name English',
			'name_dhivehi' => 'Name Dhivehi',
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Routes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}




}
