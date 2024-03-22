<?php

/**
 * This is the model class for table "navigation".
 *
 * The followings are the available columns in table 'navigation':
 * @property integer $id
 * @property integer $parent_id
 * @property integer $app_action_id
 * @property integer $visible
 * @property string $display_text
 *
 * The followings are the available model relations:
 * @property AppActions $appAction
 * @property Navigation $parent
 * @property Navigation[] $navigations
 */
class Navigation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'navigation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['display_text', 'required'],
			['parent_id, app_action_id, visible', 'numerical', 'integerOnly'=>true],
			['display_text', 'length', 'max'=>50],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, parent_id, app_action_id, visible, display_text', 'safe', 'on'=>'search'],
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
			'appAction' => [self::BELONGS_TO, 'AppActions', 'app_action_id'],
			'parent' => [self::BELONGS_TO, 'Navigation', 'parent_id'],
			'navigations' => [self::HAS_MANY, 'Navigation', 'parent_id'],
    ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'parent_id' => 'Parent',
			'app_action_id' => 'App Action',
			'visible' => 'Visible',
			'display_text' => 'Display Text',
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
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('app_action_id',$this->app_action_id);
		$criteria->compare('visible',$this->visible);
		$criteria->compare('display_text',$this->display_text,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
    ]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Navigation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
