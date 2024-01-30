<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\tables;
use Yii;
use app\models\zakaz\ZakazConstants;
/**
 * Description of DependTable
 *
 * @author Александр
 */
//use \app\models\tables\DependTables;
class DependTable extends \yii\db\ActiveRecord {
    public static $tblName=null;
    public function rules() {
        //sizes,format,liff_sizes
        $rVal= [
            [['id',DependTables::$reference],'integer'],
            [['name'],'string'],
            [['name'],'required','message'=>'Должно быть указано.']
        ];
        if (strpos(self::$tblName,'sizes')!==false 
                || strpos(self::$tblName,'format')!==false
                || strpos(self::$tblName,'liff_sizes')!==false){
                    $rVal[]=[['name'], 'checkFormat'];
            }
            if (in_array('article', $this->tableSchema->columnNames)){
            $rVal[]=[['article'],'string'];
        }
        return $rVal;
    }
    public function checkFormat($attribute, $params){
        $errM='Допускается международный формат бумаги (A1,A2,и тд.;B1,B2,и тд.) буква латинская! Или "Д*Ш", разделитель "*"!';
        if (in_array(strtolower($this->$attribute), array_keys(ZakazConstants::$formats))===false){
            $tmp=explode('*',$this->$attribute);
            if (count($tmp)===2){
                if ($tmp[0]===''){
                    $this->addError($attribute,'Ведите длину');
                }elseif($tmp[1]===''){
                    $this->addError($attribute,'Укажите ширину');
                }elseif (!is_numeric($tmp[0])){
                    $this->addError($attribute,'Длина не числовое значение');
                }elseif(!is_numeric($tmp[1])){
                    $this->addError($attribute,'Ширина не числовое значение');
                }
            }else{
                $this->addError($attribute,$errM);
            }
        }
    }
    public function beforeSave($insert) {
        Yii::trace('tableName->'.$this->tableName(),'DependTable');
        if (parent::beforeSave($insert)){
            if (mb_strlen($this->name)&&isset($this->name)){
                $this->name= mb_strtoupper(mb_substr($this->name, 0,1)). mb_strtolower(mb_substr($this->name, 1));
            }
            return true;
        }else{
            return false;
        }
    }
    public static function tableName() {
        return self::$tblName;
    }
    public static function createObject($tableName){
        self::$tblName=$tableName;
        return new self;
    }
//    public function beforeDelete(){
//        return true;
//    }
}
