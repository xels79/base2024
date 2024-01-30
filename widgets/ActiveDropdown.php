<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\helpers\Html;
use yii\helpers\Json;
/**
 * Description of ActiveBooton
 *
 * @author Александр
 */
class ActiveDropdown extends _ActiveDropdown{
    private $inpText=null;
    protected function createSelectedLabel(){
        if ($this->selected){
                echo \yii\helpers\VarDumper::dumpAsString($this->items,10,true);
                \yii::$app->end();
            return isset($this->items[$this->selected])?$this->items[$this->selected]:null;
        }else
            return null;
    }
    protected  function renderHiddenInput(){
        if ($this->selected===false||$this->selected===null)
            $val=$this->model?$this->model[$this->attribute]:'';
        else{
            $val='';
            if (isset($this->items[$this->selected])){
                $val=$this->selected;
                //$this->label=$this->items[$this->selected];
                if (is_array($this->items[$this->selected])){
                    if (isset($this->items[$this->selected]['label'])){
                        $this->label=$this->items[$this->selected]['label'];
                        $this->inpText=$this->items[$this->selected]['label'];
                    }
                    if (isset($this->items[$this->selected]['value'])){
                        $val=$this->items[$this->selected]['value'];
                    }
                }else{
                    $this->label=$this->items[$this->selected];
                    $this->inpText=$this->items[$this->selected];
                }
            }
        }
        $attr=$this->model?('['.$this->attribute.']'):!$this->formControlName?$this->attribute:$this->formControlName;
        if (!$this->formControlName){
            if ($this->model&&$this->attribute)
                $this->formControlName=$this->model->formName().'['.$this->attribute.']';
            else
                $this->formControlName=$this->id.'-hidden';
        }
        $rVal=Html::tag('input',null,
                           [
                               'id'=>$this->formControlID,
                               'value'=>Html::encode($val),
                               'type'=>'hidden',
                               'name'=>$this->formControlName
                            ]);
        \yii::trace(\yii\helpers\VarDumper::dumpAsString([
            'selected'=>$this->selected,
            'input'=>[
                'type'=>'hidden',
                'formControlName'=>$this->formControlName,
                'val'=>$val,
                'options'=>['id'=>$this->formControlID],
                'html'=>$rVal
            ]
        ]),'ActiveDropdown');
        return $rVal;
    }
    protected function creatLikeFilterParam(){
        return [
            'likeFilterAjaxUrl'=>$this->likeFilterAjaxUrl,
            'likeRequstVarName'=>$this->likeRequstVarName,
            'likeRequestName'=>$this->likeRequestName,
            'likeFilterVarName'=>$this->likeFilterVarName,
        ];
    }
    protected function renderInput(){
        $opt=['class'=>['form-control']];
        if ($this->pattern){
            $opt['pattern']=$this->pattern;
        }
        if ($this->labelId) $opt['id']=$this->labelId;
        if ($this->infoTag!=='input') {
            return Html::tag($this->infoTag,$this->label,$opt);
        }else{
            $opt['autocomplete']='off';
            if ($this->likeFilterAjaxUrl&&$this->likeParamAddToLink)
                $opt=  array_merge ($opt,$this->creatLikeFilterParam ());
            if ($this->placeholder) $opt['placeholder']=$this->placeholder;
            return Html::textInput('like', $this->inpText,$opt);
        }
    }
    protected function renderButton(){
        $opt=[
            'class'=>['dropdown-toggle','btn','btn-default'],
            'data-toggle'=>'dropdown',
            'aria-haspopup'=>true,
            'aria-expanded'=>false,
            'tabindex'=>-1
        ];
        if ($this->autoDisable) $opt['disabled']=true;
        return Html::tag($this->buttonTag,$this->renderCarretButton(),$opt);
    }
    protected function renderCarretButton(){
        return Html::tag('span','',['class'=>'caret']);
    }
    protected function registeScripts(){
        $tmp=[
            'menuId'=>$this->menuId,
            'labelId'=>$this->labelId,
            'formControlID'=>$this->formControlID,
            'addFunction'=>$this->addFunction,
            'changeFunction'=>$this->clickFunction,
            'prevDef'=>$this->preventDefault,
            'isInput'=>$this->infoTag==='input',
            'exactValue'=>$this->exactValue,
            'iSTIfNotFound'=>$this->inputStoreTextIfNotFound
        ];
        if ($this->formId) $tmp['formId']=$this->formId;
        $tmp['likeParam']=false;
        if ($this->likeFilterAjaxUrl&&$this->infoTag==='input'){
            if (!$this->likeParamAddToLink)
                $tmp['likeParam']=$this->creatLikeFilterParam();
            else
                $tmp['likeParam']=true;
        }
        if (!\Yii::$app->request->isAjax){
            $this->view->registerJsFile(($this->view->assetManager->publish('@app/web/js/class.js')[1]),['depends' => [\yii\web\JqueryAsset::className()]],'addClass');
            $this->view->registerJsFile(($this->view->assetManager->publish('@app/widgets/ActiveDropdown/activedropdown.js')[1]),['depends' => [\yii\web\JqueryAsset::className()]]);
            $dopOptStr= $this->addFunction
                       .",".$this->clickFunction
                       .",".$this->afterClickFunction
                       .",".$this->likeOnNewValue
                       .",".$this->likeOnAfterListUpdate
                       .",".$this->likeOnBeforListUpdate
                       .",".$this->likeOnBeforListUpdateGetParam
                       .");";
            $this->view->registerJs($this->id."=new ActiveDropDown(".Json::encode($tmp).",".$dopOptStr,\yii\web\View::POS_READY, $this->id.'_ADDJSInit');
        }
        self::$returnOptions=$tmp;
    }
}
