<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\SpendingTableTabs;

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
use app\widgets\SpendingTableTabs\assets\SpendingTableTabsAsset;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;

class SpendingTableTabs extends YearRange{
    public $options=['class'=>'stTab'];
    public $actions=[];
    public $viewFileName='';
//    private $_dateFrom;
//    private $_dateTo;
//    private $_selectId;
//    private $postYaerVarName;
//    private $curYear;
    public function init(){
        parent::init();
        SpendingTableTabsAsset::register($this->view);
        $actionKeys=['SpendingTable'=>['action','actionAdd','actionRemove'],'MainSpendingTable'=>['action']];
        if (!$this->viewFileName){
            throw new InvalidArgumentException('SpendingTableTabs::init() - Должн быть указан параметр "viewFileName"');
        }
        if (!$this->actions || !is_array($this->actions)){
            throw new InvalidArgumentException('SpendingTableTabs::init() - Должн быть указан параметр (array)"actions"');
        }else{
            $key= array_keys($actionKeys);
            for($i=0; $i<count($key);$i++){
                if (!array_key_exists($key[$i], $this->actions)){
                    throw new InvalidConfigException("SpendingTableTabs::init() - не указаны экшены для [\"{$key[$i]}\"]");
                }else{
                    foreach ($actionKeys[$key[$i]] as $actionName){
                        if (!in_array($actionName, array_keys($this->actions[$key[$i]]))){
                            throw new InvalidConfigException("SpendingTableTabs::init() - не указаны экшен \"'$actionName'\" для [\"'{$key[$i]}'\"]");
                        }
                    }
                }
            }
        }
        $this->registerScript();
    }
    private function registerScript(){
        $this->view->registerJs('new stones.widgets.SpendingTableTabs({'
                . 'widgetId:"' . $this->id . '",'
                . 'pjaxId:"stPjax' . $this->id . '",'
                . '});',yii\web\View::POS_READY, 'STJs'.$this->id);
    }
    private function renderTab($monthNum, $active){
        $date=(new \DateTime($this->curYear.'-'.($monthNum<10?'0':'').$monthNum.'-01'))->format('Y-m-d');
        return [
            'label'=>Yii::$app->formatter->asDate($date,'LLLL'),
            'active'=>$active,
            'content'=>$this->view->render($this->viewFileName,[
                'yaer'=>$this->curYear,
                'month'=>($monthNum<10?'0':'').$monthNum,
                'actions'=>$this->actions
            ])
        ];
    }
    private function renderTabs(){
        $items=[];
        $to=(int)(new \DateTime('now'))->format('m');
        $page=(int)Yii::$app->request->post('spendsMonth',Yii::$app->request->get('spendsMonth'));
        $page=$page?$page:((int)$to);
        for ($i=1;$i<=$to;$i++){
            $items[]=$this->renderTab($i,$page===$i);
        }
        return Tabs::widget([
            'id'=>'ssTabs'.$this->id,
            'items'=>$items
        ]);
    }
    public function run(){
        $rVal='';//'<p>curY:"'.$this->curYear.'"</p>';
        $rVal.=Html::tag('div',$this->renderYearSelect(),['class'=>'st-tab-sel-yaer']);
        $rVal.=Html::tag('div',Html::tag('div',$this->renderTabs(),['class'=>'st-tabs']),[
            'id'=>"stPjax{$this->id}",
            'class'=>'stt-pjax'
        ]);
        return Html::tag('div',$rVal,array_merge($this->options,['id'=>$this->id]));
    }
}
