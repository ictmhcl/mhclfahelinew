<?php

/**
 * This is the model class for table "agents".
 *
 * The followings are the available columns in table 'agents':
 * @property integer $id
 * @property string $code
 * @property integer $person_id
 * @property string $agent_phone
 * @property string $appointed_date
 * @property float $commission_percent
 * @property string $revoked_date
 * @property string $dhaairaasText
 * @property string $nameEnglish
 * @property float $totalSales
 * @property array $salesByUmra
 *
 * The followings are the available model relations:
 * @property AgentDhaairaa[] $agentDhaairaas
 * @property UmraPilgrims[] $umraPilgrims
 * @property Persons $person
 */
class Agents extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'agents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['code, person_id, agent_phone, appointed_date, commission_percent', 'required'],
			['person_id', 'numerical', 'integerOnly'=>true],
      ['commission_percent', 'numerical'],
			['code', 'length', 'max'=>10],
			['agent_phone', 'length', 'max'=>7],
			['revoked_date', 'safe'],
		];
	}

	public function getSalesByUmra() {
	  return 0;
  }

	public function __toString() {
	  return $this->getNameEnglish();
  }

  public function getNameEnglish() {
    $text = [];
    foreach($this->agentDhaairaas as $agentDhaairaa)
      $text[] = $agentDhaairaa->dhaairaa->name_english;

    return $this->code . ": " . $this->person->idName . " (" . implode(" / ",
      $text) . ")";
  }

	public function getDhaairaasText() {
	  $text = [];
	  foreach($this->agentDhaairaas as $agentDhaairaa)
	    $text[] = $agentDhaairaa->dhaairaa->name_english . (!empty
        ($agentDhaairaa->dhaairaa->islandsText)?(" ("
          .$agentDhaairaa->dhaairaa->islandsText . ")"):"");

	  return implode("</br>", $text);
  }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'agentDhaairaas' => [self::HAS_MANY, 'AgentDhaairaa', 'agent_id'],
			'umraPilgrims' => [self::HAS_MANY, 'UmraPilgrims', 'agent_id'],
			'person' => [self::BELONGS_TO, 'Persons', 'person_id'],
		];
	}

	public function getTotalSales() {
	  $totalSales = 0;
	  foreach($this->umraPilgrims as $umraPilgrim) {
	    $totalSales += Yii::app()->db->createCommand("
        SELECT sum(amount) 
        FROM umra_transactions
        WHERE umra_pilgrim_id = :umraPilgrimId AND is_cancelled = 0
      ")->queryScalar([':umraPilgrimId' => $umraPilgrim->id]);
    }
	  return $totalSales;
  }
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'code' => 'Code',
			'person_id' => 'Person',
			'agent_phone' => 'Agent Phone',
			'appointed_date' => 'Appointed Date',
			'commission_percent' => 'Commission Percent',
			'revoked_date' => 'Revoked Date',
      'dhaairaasText' => 'Dhaairaa',
      'totalSales' => 'Total Sales'
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Agents the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function behaviors() {
	return [
	'ActiveRecordDateBehavior' =>
	'application.behaviors.ActiveRecordDateBehavior',
	];
	}


}
