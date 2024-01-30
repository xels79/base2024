<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
/**
 * Description of ActiveBooton
 *
 * @author Александр
 */
class ActiveDropdownNoScript extends BaseWidget{
    public $model;
    public $attribute;
    public $label=false;            //Текст не выбрано
    public $items=[];
    public $selected=false;
    public $menuPullR='pull-right';  //
    public $menuId=false;           //id списка меню
    public $labelId=false;          //id Метки для текста выброной позиции
    public $formControlID=false;    //id скрытого поля формы
    public $addFunction="false";    //Функция при нажатие добавить
    public $clickFunction="false";  //Функция при нажатие пункта меню
    public $afterClickFunction="false";//Функция после нажатие пункта меню
    public $addUrl='#';             //
    public $clickUrl='#';           //
    public $autoDisable=false;      //Включает авто блокировку елементов управления
    public $size=false;
    private $itemToR=[];
    public static $returnOptions=null; //Указатель на переменную для возврата опций
                                       //Используется в ajax

    public function init(){
        parent::init();
        Html::addCssClass($this->options,['input-group']);
        if ($this->size) Html::addCssClass($this->options,['input-group'.$this->size]);
        if (!$this->label){
            if (isset($this->items[-2])){
                $this->label=$this->items[-2];
                unset($this->items[-2]);
            }else{
                $this->label='Выберите...';
                        
            }
        }
        if (is_array($this->items)){
            foreach(array_keys($this->items)as $key){
                if ($key>-1){
                    $url=is_array($this->clickUrl)?Url::to($this->clickUrl):$this->clickUrl;
                }else{
                    $url=is_array($this->addUrl)?Url::to($this->addUrl):$this->addUrl;
                }
                if (!is_array($this->items[$key])){
                    $this->itemToR[]=[
                        'label'=>$this->items[$key],
                        'url'=>  '#',
                        'linkOptions'=>[
                            'value'=>$key,
                            'url'=>$url
                        ]
                    ];
                }else{
                    $this->itemToR[]=[
                        'label'=>$this->items[$key]['label'],
                        'url'=>  isset($this->items[$key]['url'])?$this->items[$key]['url']:'#',
                        'linkOptions'=>[
                            'value'=>(isset($this->items[$key]['value'])?$this->items[$key]['value']:$key),
                            'url'=>$url
                        ]
                        
                    ];
                }
            }
        }
        if (!$this->formControlID){
            $this->formControlID=mb_strtolower ($this->model?$this->model->formName():$this->id).'-'.mb_strtolower ($this->attribute);
        }
        if (!$this->menuId) $this->menuId=$this->autoIdPrefix.($this->counter+1);
        if (!$this->labelId) 
            $this->labelId='lb_'.$this->menuId;
            
    }
    protected  function renderHiddenInput(){
        if (!$this->selected)
            $val=$this->model?$this->model[$this->attribute]:'';
        else{
            $val='';
            if (isset($this->items[$this->selected])){
                $val=$this->selected;
                if (is_array($this->items[$this->selected])){
                    if (isset($this->items[$this->selected]['value']))
                        $val=$this->items[$this->selected]['value'];
                }
            }
        }
        $attr=$this->model?'['.$this->attribute.']':$this->attribute;
        return Html::input('hidden',
                           ($this->model?$this->model->formName():$this->id).$attr,
                           $val,
                           ['id'=>$this->formControlID]);
        //else
          //  return '';
    }
    protected function renderInput(){
        $opt=['class'=>['form-control']];
        if ($this->labelId)$opt['id']=$this->labelId;
        return Html::tag('span',$this->label,$opt);
    }
    protected function renderButton(){
        $opt=[
            'class'=>['dropdown-toggle','btn','btn-default'],
            'data-toggle'=>'dropdown',
            'aria-haspopup'=>true,
            'aria-expanded'=>false            
        ];
        if ($this->autoDisable) $opt['disabled']=true;
        return Html::tag('button',$this->renderCarretButton(),$opt);
    }
    protected function renderCarretButton(){
        return Html::tag('span','',['class'=>'caret']);
    }
    protected function registeScripts(){
    }
    public function run(){
        //$this->registeScripts();
        $this->content.=$this->renderHiddenInput();
        $this->content.=$this->renderInput();
        $opt=['items'=>$this->itemToR,'encodeLabels'=>false,'options'=>['class'=>($this->menuPullR?' '.$this->menuPullR:'')]];
        if ($this->menuId) $opt['id']=$this->menuId;
        $this->content.=Html::tag('div',$this->renderButton().\yii\bootstrap\Dropdown::widget($opt),['class'=>'input-group-btn']);//pull-right
        return parent::run();
    }
}
