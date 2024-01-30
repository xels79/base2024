<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SpendingToYourself
 *
 * @author Алесандр
 */
namespace app\widgets\SpendingToYourself;
use Yii;
use yii\helpers\Html;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use app\widgets\SpendingToYourself\assets\SpendingToYourselfAsset;

class SpendingToYourself extends Widget{
    public $date='';
    public $modelName='';
    public $value='';
    public $primaryKeyName='id';
    public $dataColumnName='';
    public $actionUrl="";
    public $inputId="";
    private $itemId=0;
    public function init(){
        parent::init();
        SpendingToYourselfAsset::register($this->view);
        if (!$this->date){
            $this->date=Yii::$app->formatter->asDate(time(),'php:Y-m-d');
        }
        $this->itemId=(int)Yii::$app->formatter->asDate($this->date,'php:Ym');
        if (!$this->modelName){
            throw new InvalidConfigException('Не указуно имя модели!');
        }
        if (!$this->dataColumnName){
            throw new InvalidConfigException('Не указуно имя колонки с данными!');
        }
        if (!$this->inputId){
            $this->inputId=$this->id."_input";
        }
    }
    private function registerScript(){
        $model= new $this->modelName;
        $fName=$model->formName();
        $name=$this->id.'_controller';
        $scr="var $name=new stones.widgets.SpendingToYourself({";
        $scr.="\"action\":\"$this->actionUrl\",";
        $scr.="\"keyColumnName\":\"$this->primaryKeyName\",";
        $scr.="\"dataColumnName\":\"$this->dataColumnName\",";
        $scr.="\"dataFormName\":\"$fName\",";
        $scr.="\"targetId\":\"$this->inputId\",";
        $scr.="\"keyColumnValue\":\"$this->itemId\"";
        $scr.="});";//
        $this->view->registerJs($scr);
    }
    private function prepareValue(){
        $model= new $this->modelName;
        if ($model=$model->find()->where([$this->primaryKeyName=>$this->itemId])->one()){
            $this->value=$model->getAttribute($this->dataColumnName);
        }
    }
    public function run(){
        $this->prepareValue();
        $this->registerScript();
        return $this->view->renderFile('@app/widgets/SpendingToYourself/view.php',[
            'date'=>$this->itemId,
            'value'=>$this->value,
            'inputId'=>$this->inputId
        ]);
    }
}
