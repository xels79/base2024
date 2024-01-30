<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use app\widgets\BaseWidget;
use app\widgets\Glyphicon\GroupGlyphiconButton;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
/**
 * Description of ActiveListBox
 *
 * @author Александер
 */
class ActiveListBox extends BaseWidget{
    public $items=[];
    public $height=100;
    public $formToSaveId=null;
    public $pageName='Назад';
    public $addKeyId=null;
    public $modalTarget=null;      //id Всплывающего окна
    public $changeAction=null;     //Url для акции изменить
    public $changeFunction=null;   //Функция для акции изменить
    public $removeFunction=null;   //Функция для акции удалить
    public $removeAsk='Удалить элемент?';
    public $updateThisAction=null; //Экшин для обновления
    public function init(){
        parent::init();
        Html::addCssClass($this->options,'aListBox');
    }
    
    protected function registerScripts(){
        $tmp=[
            'wId'=>$this->id,
            'formToSaveId'=>$this->formToSaveId,
            'removeAsk'=>$this->removeAsk
        ];
        if ($this->updateThisAction){
            //$tmp['updateItemId']=$this->updateItemId;
            $tmp['updateThisAction']=$this->updateThisAction;
        }
        if ($this->modalTarget) $tmp['modalTarget']=$this->modalTarget;
        if ($this->changeAction&&$this->modalTarget) $tmp['changeAction']=$this->changeAction;
        if ($this->addKeyId) $tmp['addKeyId']=$this->addKeyId;
        if (!\Yii::$app->request->isAjax){
            $this->view->registerCssFile($this->view->assetManager->publish('@app/widgets/ActiveListBox/activelistbox.css')[1]);
            $this->view->registerJsFile(($this->view->assetManager->publish('@app/widgets/ActiveListBox/activelistbox.js')[1]),['depends' => [\yii\web\JqueryAsset::className()]]);
            
            $this->view->registerJs("$.fn.activeListBoxInit('".  json_encode($tmp)."',".($this->changeFunction?$this->changeFunction:"null").",".($this->removeFunction?$this->removeFunction:"null").");",\yii\web\View::POS_READY, $this->id.'_ALBJSInit');
        }
    }
//    protected function renderControllElement(){
//        
//    }
    protected function renderItem($key){
        if (!is_array($this->items[$key]))
            return Html::tag('a',$this->items[$key],['class'=>'list-group-item','href'=>'#','value'=>$key]);
        else{
            $label=ArrayHelper::getValue($this->items[$key],'label','');
            $opt=ArrayHelper::getValue($this->items[$key],'options',[]);
            Html::addCssClass($opt,'list-group-item');
            
            $aopt=[];
            if (isset($this->items[$key]['linkOptions']))
                if (is_array($this->items[$key]['linkOptions']))
                    $aopt=$this->items[$key]['linkOptions'];
            $aopt['href']=ArrayHelper::getValue($this->items[$key],'href','#');
            $aopt['value']=ArrayHelper::getValue($this->items[$key],'value',$key);
            if (isset($this->items[$key]['title'])){
                $aopt['data-toggle']='popover';
                $aopt['data-content']=$this->items[$key]['title'];
            }
            if (!$control=ArrayHelper::getValue($this->items[$key],'control',false)){
                return Html::tag('a',$label,  array_merge($opt,$aopt));   
            }else{
                $grOpt=is_array($control)?$control:[];
                $grOpt['tooltip']=true;
                $grOpt['tableId']=$key;
                if ($this->modalTarget&&$this->changeAction){
                    $grOpt['changeButton']=ArrayHelper::getValue($grOpt,'changeButton',true);
                }
                return Html::tag('div',
                       Html::tag('a',$label,$aopt).
                       GroupGlyphiconButton::widget(array_merge(['size'=>'xs'],$grOpt)),
                    $opt);
            }
        }
    }
    protected function renderItems(){
        $rVal='';
        foreach (array_keys($this->items) as $key){
            $rVal.=$this->renderItem($key);
        }
        return $rVal;
    }
    public function run(){
        $this->registerScripts();
        $this->content.=Html::tag('div',$this->renderItems(),['class'=>'list-group','style'=>'height:'.$this->height.'px;']);
        return parent::run();
    }
}
