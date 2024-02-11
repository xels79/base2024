<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;
use Yii;
use yii\base\Action;
use app\models\tables\DependTables;
use app\models\tables\DependTable;
use app\models\tables\Tablestypes;

/**
 * Description of ZBumFormats
 *
 * @author Александр
 */
class ZBumFormats extends Action{
    public $hasError=false;
    public $errorContent=[];
    private $mat_id;
    private $type_id;
    private $type;
    public $bumformat=null;
    public $retValue;
    public $bumName;
    public function calkBumFormat(){
        if ($this->bumformat!==null) return;
        if (!$this->mat_id=Yii::$app->request->post('mat_id')){
            $this->errorContent= ['error'=>true,'errorText'=>'Не передан id материала','errorHead'=>'Ошибка данных'];
            $this->hasError=true;
            return;
        }
        if (!$this->type_id=Yii::$app->request->post('type_id')){
            $this->errorContent= ['error'=>true,'errorText'=>'Не передан id типа материала','errorHead'=>'Ошибка данных'];
            $this->hasError=true;
            return;
        }
        if (!$this->type= Tablestypes::findOne((int)$this->type_id)){
            $this->errorContent= ['error'=>true,'errorText'=>"Материал с id='$this->type_id' не найден",'errorHead'=>'Ошибка данных'];
            $this->hasError=true;
            return;
        }   
        if ($this->hasError) return;
        $tmp_types= \yii\helpers\Json::decode($this->type->struct);
        $model=null;
        for ($i=count($tmp_types)-1;$i>-1&&mb_stripos($tmp_types[$i],'Размер')===false;$i--){
            $this->findModel($this->type->translitName,DependTables::structBas()[$tmp_types[$i]]['tblNamePart'],$model,$this->mat_id);
        }
        if ($i<0){
            $this->errorContent= ['error'=>true,'errorText'=>"Материал не имеет размера",'errorHead'=>'Ошибка данных'];
            $this->hasError=true;
            return;
        }
        $this->findModel($this->type->translitName,DependTables::structBas()[$tmp_types[$i]]['tblNamePart'],$model,$this->mat_id);
        $tmp=$this->expl($model->name);
        if (count($tmp)<2){
            $this->errorContent= ['error'=>true,'errorText'=>"Размер материала не распознан.",'errorHead'=>'Ошибка данных'];
            $this->hasError=true;
            return;
        }elseif(!$tmp[0] || !$tmp[1]){
            $tmp=$this->paperFormat($model->name);
        }
        $this->bumformat=$tmp;
        $this->bumName=$model->name;
        $this->retValue=$tmp;        
    }
    public function init(){
        parent::init();
        Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $this->calkBumFormat();
    }
    public function paperFormat($str){
        $sign= mb_strtolower(mb_substr($str, 0,1));
        $num=(int) mb_substr($str, 1);
        if (strtolower($str)==='sr3')return [320,450];
        //return [$num,$sign,$str];
        $tmp=[
            ['a'=>[841,1189],'а'=>[841,1189],'b'=>[1000,1414],'c'=>[917,1297]],
            ['a'=>[594,841],'b'=>[707,1000],'c'=>[648,917]],
            ['a'=>[420,594],'b'=>[500,707],'c'=>[458,648]],
            ['a'=>[297,420],'b'=>[353,500],'c'=>[324,458]],
            ['a'=>[210,297],'b'=>[250,353],'c'=>[229,324]],
            ['a'=>[148,210],'b'=>[176 ,250],'c'=>[162 ,229]],
            ['a'=>[105,145]]
        ];
        return isset($tmp[$num][$sign])?$tmp[$num][$sign]:[$sign,$num];
    }
    
    public function expl($str){
        $rVal=[''];
        $pos=0;
        for($i=0;$i<strlen($str)&&$pos<2;$i++){
            if (ord($str[$i])<48||ord($str[$i])>57){
                $pos++;
                if ($pos<2) $rVal[$pos]='';
            }else{
                $rVal[$pos].=$str[$i];
            }
        }
        return $rVal;
    }
    protected function findModel($mainName,$translitName,&$model,$default){
        $model=DependTable::createObject(DependTables::creatFullTableName($mainName ,$translitName))->findOne(!$model?(int)$default:(int)$model->reference_id);
    }
    public function run(){
        if (!$this->hasError){
            return $this->retValue;
        }else{
            return $this->errorContent;
        }
    }
}
