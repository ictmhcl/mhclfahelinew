<?php

/**
 * This is the model class for table "z_islands".
 *
 * The followings are the available columns in table 'z_islands':
 * @property integer $island_id
 * @property integer $atoll_id
 * @property string $name_english
 * @property string $name_dhivehi
 * @property integer $is_inhibited
 * @property string $post_code
 *
 * The followings are the available model relations:
 * @property ApplicationForms[] $applicationForms
 * @property ApplicationForms[] $applicationForms1
 * @property ApplicationForms[] $applicationForms2
 * @property ApplicationForms[] $applicationForms3
 * @property ApplicationForms[] $applicationForms4
 * @property ZAtolls $atoll
 */
class ZIslands extends CActiveRecord
{

  public function getAtollIsland() {
    return $this->atoll->abbreviation_english . ". " . $this->name_english;
  }

  public function getAtollIslandDhivehi() {
    return $this->atoll->abbreviation_dhivehi . ". " . $this->name_dhivehi;
  }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'z_islands';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['atoll_id, name_english, is_inhibited', 'required'],
			['atoll_id, is_inhibited', 'numerical', 'integerOnly'=>true],
			['name_english, name_dhivehi', 'length', 'max'=>255],
			['post_code', 'length', 'max'=>25],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['island_id, atoll_id, name_english, name_dhivehi, is_inhibited, post_code', 'safe', 'on'=>'search'],
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
			'applicationForms' => [self::HAS_MANY, 'ApplicationForms', 'emergency_contact_perm_address_island'],
			'applicationForms1' => [self::HAS_MANY, 'ApplicationForms', 'mahram_perm_address_island_id'],
			'applicationForms2' => [self::HAS_MANY, 'ApplicationForms', 'perm_address_island_id'],
			'applicationForms3' => [self::HAS_MANY, 'ApplicationForms', 'replacement_perm_address_island_id'],
			'applicationForms4' => [self::HAS_MANY, 'ApplicationForms', 'vaarutha_perm_address_island_id'],
			'atoll' => [self::BELONGS_TO, 'ZAtolls', 'atoll_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'island_id' => 'Island',
			'atoll_id' => 'Atoll',
			'name_english' => 'Name English',
			'name_dhivehi' => 'Name Dhivehi',
			'is_inhibited' => 'Is Inhibited',
			'post_code' => 'Post Code',
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

		$criteria->compare('island_id',$this->island_id);
		$criteria->compare('atoll_id',$this->atoll_id);
		$criteria->compare('name_english',$this->name_english,true);
		$criteria->compare('name_dhivehi',$this->name_dhivehi,true);
		$criteria->compare('is_inhibited',$this->is_inhibited);
		$criteria->compare('post_code',$this->post_code,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZIslands the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
