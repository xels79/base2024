<?php 

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use app\widgets\BaseWidget;
use app\widgets\GroupInput;
use app\widgets\Glyphicon\AddGlyphiconButton;
use yii\base\UnknownPropertyException;
use yii\base\InvalidConfigException;
use yii\bootstrap\Modal;
/*
Управление списком телефонов


*/
class ActiveFones extends BaseWidget{
    public $model;
    public $attribute;
    public $inputId=true;       //Йди поля для сохранения результата
                                //авто $model->tableName()."-".$attribute
                                //false блокирует использование.
    public $foneNames=[         //Имена полей по умолчанию
        'Основной',
        'Мобильный',
        'Рабочий',
        'Дополнительный'
    ];
    public static $returnOptions=null; //Указатель на переменную для возврата опций
                                       //Используется в ajax
    public $canAdd=false ;      //Разрешить добовление ещё полей
    public $neFieldNamePerfix='Дополнительный_';  //Перфикс нового поля
    public $header=false;       //Заголовок
    public $buttonAddOptions=[];
    public $buttonDeleteOptions=[];
    public $short=false;
    
    public $editable=true;
    public $panel=true;
    public $errorMess='Поле не может быть пустым или недопустимые символы!';
    private $jsonVal;
    private $line=0;
    public function init(){
        parent::init();
        if (!$this->model){
            throw new InvalidConfigException('Не задана модель!');
        }
        if (!$this->attribute){
            throw new InvalidConfigException('Не задан атрибут модели!');
        }
        if (!isset($this->model[$this->attribute]))
            throw new UnknownPropertyException('Не известный атрибут "'.$this->attribute.'" в модели "'.$this->model->className().'"');
        if ($this->inputId===true)
            $this->inputId=(is_object($this->model)?$this->model->tableName().'-':''). $this->attribute;
        $this->jsonVal=Json::decode($this->model[$this->attribute]);
        $this->buttonDeleteOptions=  array_merge(['class'=>'btn btn-danger','title'=>'Удалить'],$this->buttonDeleteOptions);
        if ($this->panel) {
                Html::addCssClass($this->options,'panel panel-default');
                //Html::addCssStyle($this->options,'margin: 0 15px;');
        }
    }
    
    protected function drawLineWidget($id,$options=[],$key='',$value='',$line=-1,$showErr=false){
        $keyVal=$key;
        if ($this->short){
            $addOpt=['style'=>'width:70px;'];
            if (mb_strlen($keyVal)>4){
                $keyVal=mb_substr($keyVal,0,3).'...';
                $addOpt['title']=$key;
            }
        }else{
            $addOpt=['style'=>'width:120px;'];
            if (mb_strlen($keyVal)>10){
                $keyVal=mb_substr($keyVal,0,7).'...';
                $addOpt['title']=$key;                
            }
        }
        $addOpt['class']='active-fone-label';
        $rVal= GroupInput::widget([
            'id'=>$id,//$this->id.'_'.$this->line,
            'inputOptions'=>!$this->editable?['readonly'=>true]:[],
            'addon'=>[
                'content'=>$keyVal,
                'options'=>$addOpt
                ],
            'value'=>$value,//$this->jsonVal[$key],
            'button'=>$this->editable?[
                    'content'=>HTML::tag('span','',['class'=>'glyphicon glyphicon-remove-sign']),
                    'options'=>  array_merge($this->buttonDeleteOptions,[
                        'id'=>$this->id.'_remove_'.$line,
                        $this->id.'_'.'toremove'=>$line,
                            ])
                ]:false,
            'options'=>$options,//['style'=>'margin-top:3px;',$this->id.'_role'=>'line'],
            'template'=>'{addon}{input}{button}'
            ]);
        if ($showErr&&$this->errorMess&&$this->editable)
            $rVal.=HTML::tag('div','',['class'=>'help-block','style'=>'display:none;','id'=>$id.'_error']);
        return $rVal;
    }
    protected function drawLine($key){
        $this->content.=$this->drawLineWidget(
                $this->id.'_'.$this->line,
                ['style'=>'margin-top:3px;',$this->id.'_role'=>'line'],
                $key,
                $this->jsonVal[$key],
                $this->line++,
                true
            );       
    }


    protected function registerScripts(){
        $tmp=[
            'fieldID'=>$this->inputId,
            'widgetID'=>$this->id,
            'valueList'=>$this->jsonVal,
            'foneNames'=>array_diff($this->foneNames, array_keys($this->jsonVal)),
            'elCount'=>  $this->line,
            'errorMess'=>  $this->errorMess,
            
        ];
        if (!\Yii::$app->request->isAjax){
            $this->view->registerJsFile(($this->view->assetManager->publish('@app/widgets/ActiveFones/activefone.js')[1]),['depends' => [\yii\web\JqueryAsset::className()]]);
            $this->view->registerJs("$.fn.activefoneInit('".Json::encode($tmp)."');",\yii\web\View::POS_READY, $this->id.'_AFJSInit');
        }
        self::$returnOptions=$tmp;
    }
    protected function drawModal($open_button_options){
        Modal::begin([
            'header' => '<h2>Hello world</h2>',
            'toggleButton' => ['label' => HTML::tag('span','',['class'=>'glyphicon glyphicon-plus','options'=>$open_button_options])],
            'footer'=>'test',
        ]);
        Modal::end();
    }
    public function run(){
        if ($this->header)
            $this->content.=$this->panel?HTML::tag('div',  HTML::tag('span',$this->header,['class'=>'panel-heading active-label']),['class'=>'panel-heading']):tag('h3',$this->header);
        $this->content.=HTML::beginTag('div',$this->panel?['class'=>'panel-body']:[]);
        foreach (array_keys($this->jsonVal) as $key){ $this->drawLine ($key);}
        if ($this->editable){
            if (!isset($this->buttonAddOptions['id']))
                $this->buttonAddOptions['id']=  $this->id.'_add';
            $this->content.=AddGlyphiconButton::widget(['options'=>$this->buttonAddOptions]);
            if ($this->errorMess)
                $this->content.=HTML::tag('div','',['class'=>'help-block-toAdd','style'=>'display:none;','id'=>$this->id.'_error']);
        }
        if ($this->editable){
            $this->content.=$this->drawLineWidget(
                    $this->id.'_add_with_label',
                    ['style'=>'margin-top:3px;display:none;']
                );       
        }
        $this->content.=HTML::endTag('div');
        if ($this->editable)$this->registerScripts();
        return parent::run();
    }
}