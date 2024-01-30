<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\SpendingTableTabs;

/**
 * Description of YearRange
 *
 * @author Алесандр
 */
use Yii;
use yii\helpers\Html;
use yii\base\Widget;

class YearRange extends Widget{
    protected $_dateFrom;
    protected $_dateTo;
    protected $_selectId;
    protected $postYaerVarName;
    protected $curYear;
    public function init(){
        parent::init();
        $this->_dateFrom=$this->_dateFrom?$this->_dateFrom:$this->_dateFrom=new \DateTime('2020-01-01');
        $this->_dateTo=new \DateTime((new \DateTime('now'))->format('Y').'-'.(new \DateTime('now'))->format('m').'-01');
        $this->postYaerVarName = $this->postYaerVarName ? $this->postYaerVarName : ( 'selectYaer'.$this->id );
        $this->_selectId=$this->postYaerVarName.'ID';
        $this->curYear=(int)Yii::$app->request->post($this->postYaerVarName,Yii::$app->request->get($this->postYaerVarName,$this->_dateTo->format('Y')));
    }
    protected function renderYearSelect(){
        $items=[];
        $to=(int)$this->_dateTo->format('Y');
        for ($i=(int)$this->_dateFrom->format('Y');$i<=$to;$i++){
            $items[$i]="{$i}г.";
        }
        $label=Html::tag('label','Показать таблицу за год...',['for'=>$this->_selectId,'class'=>'control-label']);
        $dd=Html::dropDownList($this->postYaerVarName,$this->curYear,$items,[
            'id'=>$this->_selectId,
            'class'=>'form-control'
        ]);
        return Html::tag('div',$label.$dd,['class'=>'form-group']);
    }
}
