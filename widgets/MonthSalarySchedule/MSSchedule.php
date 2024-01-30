<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\MonthSalarySchedule;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use app\widgets\SpendingTableTabs\YearRange;
use app\models\ZarplatMoth;
use app\models\Zarplata;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;

use app\widgets\MonthSalarySchedule\assets\MSScheduleAssets;

/**
 * Description of MonthlSsalarySchedule
 *
 * @author Алесандр
 * @version 0.1
 */
class MSSchedule extends YearRange{
    const colors=[
        '#FFFF00',
        '#660000',
        '#669900',
        '#333300',
        '#CCFF00',
        '#FF3300',
        '#666600',
        '#66FF00',
        '#336600',
        '#FF9900',
        '#FF0000',
        '#669900',
        '#33FF00',
        '#CCCCFF',
        '#CC33FF',
        '#9933FF',
        '#0099FF',
        '#CCFF66',
        '#CC3366',
        '#00CC00',
        '#ffOOCC',
        '#33FFFF',
        '#FF6666'
    ];
    public function init(){
        $this->_dateFrom=new \DateTime('2019-01-01');
        parent::init();
        MSScheduleAssets::register($this->view);
    }
    
    private function prepareData(){
        $rVal=[];
        $rVal2=[];
        $index=0;
        $names=[];
        $color=0;
        $query= Zarplata::find()
                ->where(['zarplat_moth.year'=>$this->curYear])
                ->leftJoin('zarplat_moth', 'zarplata.month_id=zarplat_moth.id')
                ->leftJoin('managerOur', 'zarplata.name=managerOur.name')
                ->with('month')
                ->with('userCard')
                ->all();
        
        foreach ($query as $el){
            if ( ( (double)$el['wages']>0 || ( ((double)$el['normal']>0 || (double)$el['hours']>0 ) && ((int)$el->userCard->payment_id)<3)  ) && mb_strpos($el['name'], '%')===false ){
                $m=(int)$el->month->month;
                if (!array_key_exists($m, $rVal)){
                    $rVal[$m] = [];
                }
                $rVal[$m][$el['name']]=$el->wagesOnly;
            }
        }
        foreach ($rVal as $mK=>$el){
            foreach ($el as $name=>$wages){
                if (!array_key_exists($name, $names)){
                    $names[$name]=$index;
                    $rVal2[$index++]=[
                       'type'=>'column',
                       'name'=>$name,
                       'legendText'=>$name,
                       'showInLegend'=>true,
                       'color'=>self::colors[$color++],
                       'dataPoints'=>[
                           ['label'=>1,'y'=>0],
                           ['label'=>2,'y'=>0],
                           ['label'=>3,'y'=>0],
                           ['label'=>4,'y'=>0],
                           ['label'=>5,'y'=>0],
                           ['label'=>6,'y'=>0],
                           ['label'=>7,'y'=>0],
                           ['label'=>8,'y'=>0],
                           ['label'=>9,'y'=>0],
                           ['label'=>10,'y'=>0],
                           ['label'=>11,'y'=>0],
                           ['label'=>12,'y'=>0]
                        ]
                    ];
                    if ($color>count(self::colors)){
                        $color=0;
                    }
                }
                $cInex=$names[$name];
                $rVal2[$cInex]['dataPoints'][(int)$mK-1]=[
                    'label'=>(int)$mK,
                    'y'=>(double)$wages
                ];
                
            }
        }
        $rVal2[]=[
            'type'=>'column',
            'name'=>"Все1",
            'legendText'=>"Покозать все",
            'visible'=>false,
            'showInLegend'=>true,
            'color'=>'#E0E0E0',
            'dataPoints'=>[
                ['label'=>1,'y'=>0],
                ['label'=>2,'y'=>0],
                ['label'=>3,'y'=>0],
                ['label'=>4,'y'=>0],
                ['label'=>5,'y'=>0],
                ['label'=>6,'y'=>0],
                ['label'=>7,'y'=>0],
                ['label'=>8,'y'=>0],
                ['label'=>9,'y'=>0],
                ['label'=>10,'y'=>0],
                ['label'=>11,'y'=>0],
                ['label'=>12,'y'=>0]
             ]
         ];
        $rVal2[]=[
            'type'=>'column',
            'name'=>"Все2",
            'legendText'=>"Скрыть все",
            'visible'=>false,
            'showInLegend'=>true,
            'color'=>'#E0E0E0',
            'dataPoints'=>[
                ['label'=>1,'y'=>0],
                ['label'=>2,'y'=>0],
                ['label'=>3,'y'=>0],
                ['label'=>4,'y'=>0],
                ['label'=>5,'y'=>0],
                ['label'=>6,'y'=>0],
                ['label'=>7,'y'=>0],
                ['label'=>8,'y'=>0],
                ['label'=>9,'y'=>0],
                ['label'=>10,'y'=>0],
                ['label'=>11,'y'=>0],
                ['label'=>12,'y'=>0]
             ]
         ];
        $rVal2[]=[
            'type'=>'column',
            'name'=>"Все3",
            'legendText'=>"Инвертировать",
            'visible'=>false,
            'showInLegend'=>true,
            'color'=>'#E0E0E0',
            'dataPoints'=>[
                ['label'=>1,'y'=>0],
                ['label'=>2,'y'=>0],
                ['label'=>3,'y'=>0],
                ['label'=>4,'y'=>0],
                ['label'=>5,'y'=>0],
                ['label'=>6,'y'=>0],
                ['label'=>7,'y'=>0],
                ['label'=>8,'y'=>0],
                ['label'=>9,'y'=>0],
                ['label'=>10,'y'=>0],
                ['label'=>11,'y'=>0],
                ['label'=>12,'y'=>0]
             ]
         ];
        return $rVal2;//$query;//ArrayHelper::map($query,'name','wages','month');
    }

    private function registerScript(){
        $this->view->registerJs('new stones.widgets.MSS({'
                . 'widgetId:"' . $this->id . '",'
                . 'chartId:"chart' . $this->id . '",'
                . 'selectId:"' . $this->_selectId . '",'
                . 'year:"' . $this->curYear . '",'
                . 'data:' . \yii\helpers\Json::encode($this->prepareData()) . ','
                . '});',yii\web\View::POS_READY, 'MSSJs'.$this->id);
    }
    private function postAnswer(){
        return \yii\helpers\Json::encode([
            'status'=>'ok',
            'widgetId'=>$this->id,
            'data'=>$this->prepareData(),
            'yaer'=>$this->curYear
        ]);
    }
    
    public function run(){
        if (Yii::$app->request->isPost){
            return $this->postAnswer();
        }else{
            $this->registerScript();
            $rVal = Html::tag('h1','Зарплата',['style'=>['padding-top'=>'21px']]);
            $rVal .= Html::tag('div',$this->renderYearSelect(),['class'=>'MSS-sel-yaer']);
            $rVal .= Html::tag('div','',['id'=>'chart'.$this->id,'class'=>'chart-holder']);
            return Html::tag('div',$rVal,['class'=>'MSS','id'=>$this->id]);
        }
    }
}
