<?php

/**
 * This is the model class for table "z_user_states".
 *
 * The followings are the available columns in table 'z_user_states':
 * @property integer $user_state_id
 * @property string $name_english
 *
 * The followings are the available model relations:
 * @property Users[] $users
 */
class ZUserStates extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'z_user_states';
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
			['name_english', 'length', 'max'=>50],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['user_state_id, name_english', 'safe', 'on'=>'search'],
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
			'users' => [self::HAS_MANY, 'Users', 'user_state_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'user_state_id' => 'User State',
			'name_english' => 'Name English',
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

		$criteria->compare('user_state_id',$this->user_state_id);
		$criteria->compare('name_english',$this->name_english,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZUserStates the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
