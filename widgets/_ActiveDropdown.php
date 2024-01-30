<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Description of _ActiveDropdown
 *
 * @author Александр
 */
class _ActiveDropdown extends BaseWidget {
    public $model;
    public $attribute;
    public $label=false;                //Текст не выбрано
    public $hideLabel=false;            //Скрыть метку
    public $items=[];
    public $selected=false;
    public $menuPullR='pull-right';     //
    public $menuId=false;               //id списка меню
    public $labelId=false;              //id Метки для текста выброной позиции
    public $formControlID=false;        //id скрытого поля формы
    public $formControlName=false;      //Атрибут name скрытого поля
    public $addFunction="false";        //Функция при нажатие добавить
    public $clickFunction="false";      //Функция при нажатие пункта меню
    public $afterClickFunction="false"; //Функция после нажатие пункта меню
    public $addUrl='#';                 //
    public $clickUrl='#';               //
    public $autoDisable=false;          //Включает авто блокировку елементов управления
    public $size=false;
    public $noScript=null;
    public $infoTag='span';
    public $pattern=false;
    public $formId=false;               //ID Формы
    public $exactValue=false;           //Должно быть введино точное значения
    public $inputStoreTextIfNotFound=false;//Сохранять текст если не найдено значение
    //public $штзгеOptions=[];            //Опции тэга input действует если infoTag='input'
    
    public $placeholder=false;          //Для input
    
    /*                                  ДЛЯ ФИЛЬТРА                                         */
    
    public $likeFilterAjaxUrl=false;    //Url для фильра
    public $likeRequstVarName='rq';     //Имя переменной для передачи названия ajax запроса
                                        //на сервер, в случaе необходимости
    public $likeRequestName=false;      //Название запроса на сервер тоже если надо
    public $likeFilterVarName='like';   //Имя переменной для $filterLikeAjaxUrl
    public $likeParamAddToLink=false;   //Добавить параметры к тэгу <a> или только через init()
    public $caseSensitivity=false;      //С учётом регистра
    public $likeOnAfterListUpdate="false";
    public $likeOnNewValue="false";
    public $likeOnBeforListUpdate="false";
    public $likeOnBeforListUpdateGetParam="false"; //Callback Возвращает дополнительные
                                                   //параметры для запроса
    public $buttonTag='button';
    public $preventDefault=true;
    public $debug=false;
    protected $itemToR=[];
    public static $returnOptions=null;  //Указатель на переменную для возврата опций
                                        //Используется в ajax
        private function compute_label($like,$txtVal){ //Вычисляет метку с учотом post или get переменной $likeFilterVarName
        $lbl=false;
        $likeBack=$like;
        if ($this->caseSensitivity===false){
            $like=mb_strtolower($like);
            $txt=mb_strtolower($txtVal);
        }else{
            $txt=$txtVal;
        }
        $likeSz=mb_strlen($like);
        if (($pos=mb_strpos($txt,$like))!==false){
            //$exp=explode($like,$txt,2);
            $exp[0]=mb_substr($txtVal,0,$pos);
            $exp[1]=mb_substr($txtVal,$pos+$likeSz,  mb_strlen($txtVal)-$pos-$likeSz);
            $exp[3]=mb_substr($txtVal,$pos,$likeSz);
            if (count($exp)===3){
                $lbl=$exp[0].Html::tag('span',$exp[3]).$exp[1];
            }else{
                $lbl=$txtVal;
            }
        }
        return $lbl;
    }
    private function initItemNotArrayAdd($like,$key,$url){ //Пукнкт не евляется массивом
        $tmp=[
            'url'=>  '#',
            'linkOptions'=>[
                'value'=>$key,
                'url'=>$url
            ]
        ];
        if ($like!==false){
            if (($lbl=$this->compute_label($like, $this->items[$key]))!==false){
                $tmp['label']=$lbl;
                $this->itemToR[]=$tmp;
            }
        }else{
            $tmp['label']=$this->items[$key];
            $this->itemToR[]=$tmp;
        }
    }
    private function initItemIsArrayAdd($like,$key,$url){ //Пукнкт евляется массивом
        $tmp=[
            'label'=>$this->items[$key]['label'],
            'url'=>  isset($this->items[$key]['url'])?$this->items[$key]['url']:'#',
            'linkOptions'=>!isset($this->items[$key]['linkOptions'])?[
                'value'=>(isset($this->items[$key]['value'])?$this->items[$key]['value']:$key),
                'url'=>$url
            ]:array_merge(['url'=>$url],$this->items[$key]['linkOptions'])
        ];
        if ($like!==false){
            if (($lbl=$this->compute_label($like, $this->items[$key]['label']))!==false){
                $tmp['label']=$lbl;
                $this->itemToR[]=$tmp;
            }
        }else{
            $this->itemToR[]=$tmp;
        }
    }

    private function initItemsList(){
        if (!$like=Yii::$app->request->post($this->likeFilterVarName,false))
            $like=Yii::$app->request->get($this->likeFilterVarName,false);
        foreach(array_keys($this->items)as $key){
            if ($key>-1){
                $url=is_array($this->clickUrl)?Url::to($this->clickUrl):$this->clickUrl;
            }else{
                $url=is_array($this->addUrl)?Url::to($this->addUrl):$this->addUrl;
            }
            if (!is_array($this->items[$key])){
                $this->initItemNotArrayAdd($like, $key, $url);
            }else{
                $this->initItemIsArrayAdd($like, $key, $url);
            }
        }
    }

    public function init(){
        parent::init();
        if ($this->noScript===null){
            $this->noScript=\yii::$app->request->isPost;
        }
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
        if (is_array($this->items)) $this->initItemsList ();
        if (!$this->formControlID){
            $this->formControlID=mb_strtolower ($this->model?$this->model->formName():$this->id).'-'.mb_strtolower ($this->attribute?$this->attribute:'hidden');
        }
        if (!$this->menuId) $this->menuId=$this->autoIdPrefix.($this->counter+1);
        if ($this->labelId===false) 
            $this->labelId='lb_'.$this->menuId;
            
    }
    public function run(){
        if (!$this->noScript)$this->registeScripts();
        $this->content.=$this->renderHiddenInput();
        $this->content.=$this->renderInput();
        $opt=['items'=>$this->itemToR,'encodeLabels'=>false,'options'=>['class'=>($this->menuPullR?' '.$this->menuPullR:'')]];
        if ($this->menuId) $opt['id']=$this->menuId;
        $this->content.=Html::tag('div',$this->renderButton().\yii\bootstrap\Dropdown::widget($opt),['class'=>'input-group-btn']);//pull-right
        yii::trace($this->menuId,'ActiveDropDown');
        if ($this->debug){
            return \yii\helpers\VarDumper::dumpAsString($this,10,true);
            //\yii::$app->end();
        }
        return parent::run();
    }
}
