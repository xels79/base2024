<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zarplata".
 *
 * @property int $id
 * @property string $name Имя
 * @property int $month_id Дата
 * @property int $payment_id Идентификатор типа оплаты
 * @property string $wages В месяц
 * @property string $normal руб/час
 * @property string $card1 Карта1
 * @property string $card2 Карта1
 * @property string $minus Вычеты
 * @property bolean $employed Устроен/не устроен
 * @property int $hours Часы/дни
 * @property string $prize Премия
 * @property string $prepayment 22 число
 * @property string $payment1 Выдано
 * @property string $payment2 Выдано
 * @property string $payment3 Выдано
 * @property int $procats Прокаты
 */
class Zarplata extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'zarplata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'month_id', 'payment_id'], 'required'],
            [['month_id', 'payment_id', 'procats'], 'integer'],
            [['wages', 'normal', 'prize', 'prepayment', 'payment1', 'payment2', 'payment3',
            'minus', 'card1', 'card2'], 'number'],
            [['employed'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            ['name', 'myUnique']
        ];
    }

    public function getMonth(){
        return $this->hasOne(ZarplatMoth::class, ['id' => 'month_id']);
    }
    
    public function getUserCard(){
        return $this->hasOne(admin\ManagersOur::class, ['name' => 'name']);
    }
    
    public function myUnique($attribute, $params) {
        if ($this->isNewRecord && $tmp = Zarplata::find()->where([$attribute => $this->$attribute, 'month_id' => $this->month_id, 'payment_id' => $this->payment_id])->one()) {
            $this->addError($attribute, 'Имя уже задано');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'         => 'ID',
            'name'       => 'Имя',
            'month_id'   => 'Дата',
            'wages'      => 'В месяц',
            'normal'     => 'руб/час',
            'prize'      => 'Премия',
            'minus'      => 'Вычеты',
            'prepayment' => '22 число',
            'payment1'   => 'Выдано',
            'payment2'   => 'Выдано',
            'payment3'   => 'Выдано',
        ];
    }

    public function getNormalText() {
        if (mb_strpos($this->name, '%') !== false)
            return 0;
        switch ($this->payment_id) {
            case 2:
            case 4:
                return 0;
                break;
            case 3:
                return $this->procats;
                break;
            default :
                return $this->normal;
                break;
        }
    }

    public function getWagesText() {
        switch ($this->payment_id) {
            case 2:
                return round($this->wages);
                break;
            case 3:
                return round($this->procats * $this->hours);
                break;
            default :
                return round($this->normal * $this->hours);
                break;
        }
    }

    public function getSumm($paymentIdToCheck=-1) {
        $paymentIdToCheck=$paymentIdToCheck==-1?$this->payment_id:$paymentIdToCheck;
        switch ($paymentIdToCheck) {
            case 2:
                return round($this->wages - $this->payment2 - $this->payment3) + $this->prize - $this->minus - $this->card1 - $this->card2;
                break;
            case 3:
                return round($this->procats * $this->hours + $this->prize - $this->minus - $this->card1 - $this->card2 - $this->payment1 - $this->payment2 - $this->payment3);
                break;
            default :
                return round($this->normal * $this->hours + $this->prize - $this->minus - $this->card1 - $this->card2 - $this->payment1 - $this->payment2 - $this->payment3);
                break;
        }
    }
    public function getPaidOut(){
        return $this->card1 + $this->card2 + $this->payment1 + $this->payment2 + $this->payment3;
    }
    public function getPercentOnly() {
        if ($this->payment_id===4 || mb_strpos($this->name, "%")>-1){
            return round($this->normal * $this->hours + $this->prize - $this->minus);
        }else{
            return 0;
        }
    }
    public function getWagesOnly() {
        if ($this->payment_id!==4){
            switch ($this->payment_id) {
                case 2:
                    return $this->wages + $this->prize - $this->minus;
                    break;
                case 3:
                    return round($this->procats * $this->hours + $this->prize - $this->minus);
                    break;
                default:
                    return round($this->normal * $this->hours + $this->prize - $this->minus);
                    break;
            }
        }else{
            return 0;
        }
    }

    public static function totalPaidOut($year,$month){
        $zarplata=self::find()
                ->leftJoin('zarplat_moth','zarplata.month_id=zarplat_moth.id')
                ->where(['zarplat_moth.year'=>$year,
                    'zarplat_moth.month'=>$month,
                ])
                ->sum('IFNULL(zarplata.card1,0) + IFNULL(zarplata.card2,0) + IFNULL(zarplata.payment1,0) + IFNULL(zarplata.payment2,0) + IFNULL(zarplata.payment3,0)');
        return (double)$zarplata;
    }
    
    public static function totalPercent($year,$month){
        $zarplata=self::find()
                ->leftJoin('zarplat_moth','zarplata.month_id=zarplat_moth.id')
                ->where(['zarplat_moth.year'=>$year,
                    'zarplat_moth.month'=>$month,
                ])->andWhere(['or',['=','zarplata.payment_id',4],['like','zarplata.name','%\%',false]])
                ->sum('zarplata.normal * IFNULL(zarplata.hours,0) + IFNULL(`zarplata`.`prize`,0) - IFNULL(`zarplata`.`minus`,0)');
        return (double)$zarplata;
/*
        $zarplata=self::find()
                ->leftJoin('zarplat_moth','zarplata.month_id=zarplat_moth.id')
                ->where(['zarplat_moth.year'=>$year,
                    'zarplat_moth.month'=>$month,
                    'zarplata.payment_id'=>4
                ])
                ->sum('zarplata.normal * IFNULL(zarplata.hours,0) + IFNULL(`zarplata`.`prize`,0) - IFNULL(`zarplata`.`minus`,0)');
        return (double)$zarplata;
 *
 */
    }
    public static function totalWages($year,$month){
        $zarplata=self::find()
                ->leftJoin('zarplat_moth','zarplata.month_id=zarplat_moth.id')
                ->where(['zarplat_moth.year'=>$year,'zarplat_moth.month'=>$month,])
                ->andWhere(['<','zarplata.payment_id',4])
                ->sum(''
                        .'IF( `zarplata`.`payment_id`=2'
                            .',`zarplata`.`wages`+IFNULL(`zarplata`.`prize`,0)-IFNULL(`zarplata`.`minus`,0)'
                            .',IF( `zarplata`.`payment_id`=3,'
                                 .'`zarplata`.`procats` * IFNULL(`zarplata`.`hours`,0)'
                                   .' + IFNULL(`zarplata`.`prize`,0) - IFNULL(`zarplata`.minus,0)'
                                    
                                 .'  ,`zarplata`.`normal` * IFNULL(`zarplata`.`hours`,0) + IFNULL(`zarplata`.`prize`,0) - IFNULL(`zarplata`.`minus`,0)'
                            .')'
                        .')'
                );
        return (double)$zarplata;
    }

}
