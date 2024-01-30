<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\SpendingTable;

/**
 * Description of SpendingTable
 *
 * @author Алесандр
 */
use Yii;
use yii\helpers\Html;
use yii\base\Widget;
use yii\helpers\VarDumper;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use app\widgets\SpendingTable\assets\SpendingTableAsset;
use app\widgets\SpendingTable\STStaticTotalColumn;
use app\models\Zarplata;
use yii\grid\GridView;
use yii\grid\DataColumn;

class StaticSpendingTable extends Widget{
    public $options=['class'=>'st st-wages'];
    public $date='';
    private $_dateFrom;
    private $dataProvider;
    public function init(){
        parent::init();
        SpendingTableAsset::register($this->view);
        if (!$this->date){
            throw new InvalidArgumentException('SpendingTable::init() - Должна быть указана дата "date"');
        }
        $this->_dateFrom=new \DateTime((new \DateTime($this->date))->format('Y').'-'.(new \DateTime($this->date))->format('M').'-01');
        $this->prepareDataProvider();
        $this->registerScript();
    }
    private function prepareDataProvider(){
        $year=(int)$this->_dateFrom->format('Y');
        $month=(int)$this->_dateFrom->format('m');
        $zarplata=Zarplata::find()
            ->leftJoin('zarplat_moth','zarplata.month_id=zarplat_moth.id')
            ->where(['zarplat_moth.year'=>$year,'zarplat_moth.month'=>$month])
            ->orderBy('`zarplata`.`payment_id`,`zarplata`.`name`');
        $this->dataProvider=new ActiveDataProvider([
            'query'=>$zarplata,
            'pagination' => [
                'pageParam'=>'sttPage'.$this->id,
                'pageSize' => 20
            ],
        ]);
    }
    private function registerScript(){
        $this->view->registerJs('new stones.widgets.StaticSpendingTable({'
                . 'pjaxId:"stPjax' . $this->id . '",'
                . 'greedViewId:"stGW'.$this->id.'"'
                . '});',yii\web\View::POS_READY, $key = 'STJs'.$this->id);
    }
    public function run(){
        $rVal=GridView::widget([
            'id'=>'stGW'.$this->id,
            'dataProvider'=>$this->dataProvider,
            'showFooter'=>true,
            'columns' => [
                'name:text',
//                [
//                    'class'=>STStaticTotalColumn::className(),
//                    'attribute'=>'paidOut',
//                    'total'=>Zarplata::totalPaidOut((int)$this->_dateFrom->format('Y'),(int)$this->_dateFrom->format('m')),
//                    'label'=>'Выплатили',
//                    'format'=>'currency'
//                ],
                [
                    'class'=>STStaticTotalColumn::className(),
                    'attribute'=>'percentOnly',
                    'total'=>Zarplata::totalPercent((int)$this->_dateFrom->format('Y'),(int)$this->_dateFrom->format('m')),
                    'label'=>'%',
                    'format'=>'currency',
                    'footerOptions'=>['class'=>'st-percenttd']
                ],
                [
                    'class'=>STStaticTotalColumn::className(),
                    'attribute'=>'wagesOnly',
                    'total'=>Zarplata::totalWages((int)$this->_dateFrom->format('Y'),(int)$this->_dateFrom->format('m')),
                    'label'=>'Зарплата',
                    'format'=>'currency',
                    'footerOptions'=>['class'=>'st-zptd']
                ],
            ],
        ]);
        $rVal=Html::tag('div',$rVal,[
            'id'=>'stPjax'.$this->id,
            'data-pjax-container'=>"",
            'data-pjax-push-state'=>"",
        ]);
        return Html::tag('div',$rVal,array_merge($this->options,[
            'id'=>$this->id,
            'data-date'=>$this->date
        ]));
    }
}
