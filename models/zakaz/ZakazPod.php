<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazPod
 *
 * @author Александр
 */
use Yii;
use yii\db\ActiveRecord;
class ZakazPod extends ActiveRecord{
    public static function tableName() {
        return 'zakaz_pod';
    }
    
    public function rules() {
        return [
            [['pod_id', 'zakaz_id','workType'], 'integer'],
            [['coast', 'payment'], 'number'],
            [['paid'],'boolean'],
            [['isMain'],'boolean'],
            [['date_info'], 'date', 'format'=>'dd.MM.yyyy'],
            ['paid', 'default', 'value' => 0]
        ];
    }
    public function attributeLabels() {
        return [
            'workTypeText'=>'Вид работы',
        ];
    }
    public function getPod(){
        return $this->hasOne(\app\models\admin\Pod::className(),['firm_id'=>'pod_id']);
    }
    public function getFirmname(){
        if ($model= \app\models\admin\Pod::findOne((int)$this->pod_id)){
            return $model->mainName;
        }else
            return 'не найден';
    }
    public function getDate_info_text(){
        return $this->date_info?Yii::$app->formatter->asDate($this->date_info):'';
    }
    public function beforeSave($insert){
//        return true;
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->date_info){
            $this->date_info=Yii::$app->formatter->asDate($this->date_info,'php:Y-m-d');
        }
        return true;
    }
    public function fields() {
        $rVal=parent::fields();
        $rVal['date_info']='date_info_text';
        return $rVal;
    }

}
