<?php

namespace app\models;

use Yii;
use app\models\Zarplata;
use app\models\Spends;

/**
 * This is the model class for table "static_spends".
 *
 * @property int $id
 * @property string $date Дата
 * @property float|null $percent %
 * @property float|null $officeRental Аренда офиса
 * @property float|null $arsiRent Аренда АСТ
 * @property float|null $cuttingIntFFighting Резка, интернет, пожарка
 * @property float|null $utilityBills Электричество
 * @property float|null $forCars Въезд машин
 * @property float|null $bank1 Банк Альфа АСТ
 * @property float|null $bank2 Банк Альфа Астро
 * @property float|null $bank3 Банк Райф
 * @property float|null $bank4 Банк Сбер
 * @property float|null $ndsAsterio НДС Астерио
 * @property float|null $nalogZPAsterio Налоги на з/п Астерио
 * @property float|null $nalogZPAST Налоги на з/п АСТ
 * @property float|null $sixPercentAST 6% АСТ
 * @property float|null $cashWithdrawal Снятие наличных
 * @property float|null $rezerved1 Резерв
 * @property float|null $rezerved2 Резерв
 */
class StaticSpends extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'static_spends';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'required'],
            [['date'], 'safe'],
            [['percent', 'officeRental', 'arsiRent', 'cuttingIntFFighting', 'utilityBills', 'forCars', 'ndsAsterio', 'nalogZPAsterio', 'nalogZPAST', 'sixPercentAST', 'cashWithdrawal', 'rezerved1', 'rezerved2', 'bank1', 'bank2', 'bank3', 'bank4'], 'number'],
            [['percent', 'officeRental', 'arsiRent', 'cuttingIntFFighting', 'utilityBills', 'forCars', 'ndsAsterio', 'nalogZPAsterio', 'nalogZPAST', 'sixPercentAST', 'cashWithdrawal', 'rezerved1', 'rezerved2', 'bank1', 'bank2', 'bank3', 'bank4'],
                'default',
                'value' => 0
            ],
            [['date'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Дата',
            'percent' => '%',
            'officeRental' => 'Аренда Крол',
            'arsiRent' => 'Аренда Арси',
            'cuttingIntFFighting' => 'Резка, интернет, пожарка',
            'utilityBills' => 'Электричество',
            'forCars' => 'Въезд машин',
            'ndsAsterio' => 'НДС Астро',
            'nalogZPAsterio' => 'Налоги на з/п Астро',
            'nalogZPAST' => 'Налоги на з/п АСТ',
            'sixPercentAST' => '6% АСТ',
            'cashWithdrawal' => 'Снятие наличных',
            'rezerved1' => '1',
            'rezerved2' => '2',
            'bank1'=>'Банк Альфа АСТ',
            'bank2'=>'Банк Альфа Астро',
            'bank3'=>'Банк Райф',
            'bank4'=>'Банк Сбер',
            'zp'=>'з/п',
            'spends'=>'Траты в месяц',
            'total'=>'Итого расходы за месяц'
        ];
    }
    
    public function getZp(){
        if ($this->isNewRecord){
            return 0;
        }else{
            $dt=new \DateTime($this->date);
            return round(Zarplata::totalWages($dt->format('Y'),$dt->format('m')),2);
        }
    }
    public function getSpends(){
        if ($this->isNewRecord){
            return 0;
        }else{
            $dt=new \DateTime($this->date);
            return Spends::totalMonth($dt->format('Y-m-d'));
        }        
    }
    public function getTotal(){
        $rVal=$this->arsiRent+$this->cashWithdrawal+$this->cuttingIntFFighting;
        $rVal+=$this->forCars+$this->nalogZPAST+$this->nalogZPAsterio+$this->ndsAsterio;
        $rVal+=$this->officeRental+$this->percent+$this->rezerved1+$this->rezerved2+$this->sixPercentAST;
        $rVal+=$this->bank1 + $this->bank2 + $this->bank3 + $this->bank4;
        $rVal+=$this->utilityBills+$this->getZp()+$this->getSpends();
        return $rVal;
    }
    public static function totalByDate($date){
        $dt=(new \DateTime($date))->format('Y-m-01');
        if ($model=self::find()->where(['date'=>$dt])->one()){
            return $model->total;
        }else{
            return 0;
        }
    }
    public function getPercent(){
        if ($this->isNewRecord){
            return 0;
        }else{
            $dt=new \DateTime($this->date);
            return Zarplata::totalPercent($dt->format('Y'),$dt->format('m'));
        }        
    }
}
