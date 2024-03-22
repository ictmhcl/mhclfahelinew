<?php

/**
 * This is the model class for table "operation_logs".
 *
 * The followings are the available columns in table 'operation_logs':
 * @property integer $operation_log_id
 * @property integer $created_by_user_id
 * @property string $created_time
 * @property string $created_ip
 * @property integer $modified_by_user_id
 * @property string $modified_time
 * @property string $modified_ip
 * @property integer $operation_type_id
 * @property string $remarks
 * 
 * @property Users $createdUser
 * @property Users $modifiedUser
 */
class OperationLogs extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'operation_logs';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['operation_type_id', 'required'],
            ['created_by_user_id, modified_by_user_id, operation_type_id', 'numerical', 'integerOnly'=>true],
            ['created_ip, modified_ip', 'length', 'max'=>50],
            ['remarks', 'length', 'max'=>255],
            ['created_time, modified_time', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['operation_log_id, created_by_user_id, created_time, created_ip, modified_by_user_id, modified_time, modified_ip, operation_type_id, remarks', 'safe', 'on'=>'search'],
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
            'createdUser' => [self::BELONGS_TO, 'Users', 'created_by_user_id'],
            'modifiedUser' => [self::BELONGS_TO, 'Users', 'modified_by_user_id']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'operation_log_id' => 'Operation Log',
            'created_by_user_id' => 'Created By User',
            'created_time' => 'Created Time',
            'created_ip' => 'Created Ip',
            'modified_by_user_id' => 'Modified By User',
            'modified_time' => 'Modified Time',
            'modified_ip' => 'Modified Ip',
            'operation_type_id' => 'Operation Type',
            'remarks' => 'Remarks',
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

        $criteria->compare('operation_log_id',$this->operation_log_id);
        $criteria->compare('created_by_user_id',$this->created_by_user_id);
        $criteria->compare('created_time',$this->created_time,true);
        $criteria->compare('created_ip',$this->created_ip,true);
        $criteria->compare('modified_by_user_id',$this->modified_by_user_id);
        $criteria->compare('modified_time',$this->modified_time,true);
        $criteria->compare('modified_ip',$this->modified_ip,true);
        $criteria->compare('operation_type_id',$this->operation_type_id);
        $criteria->compare('remarks',$this->remarks,true);

        return new CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db_audit;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OperationLogs the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}