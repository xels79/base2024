<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;
use Yii;
use app\models\zakaz\ZakazMaterials;
/**
 * Description of ZMaterialReset
 *
 * @author Александр
 */
class ZMaterialReset extends ZAction{
    public function run(){
        if (($materialNumber=Yii::$app->request->post('materialNumber',false))===false){
            return ['status'=>'error','errorText'=>'Не верный номер материала'];
        }
        $query=ZakazMaterials::find()->where(['zakaz_id'=>$this->postID])->all();
        if (count($query)>(int)$materialNumber){
            $query[(int)$materialNumber]->order_date=null;
            $query[(int)$materialNumber]->delivery_date=null;
            if ($query[(int)$materialNumber]->save(['order_date', 'delivery_date'])){
                return ['status'=>'ok', '$materialNumber'=>$materialNumber];
            }else{
                return ['status'=>'error','errorText'=>'Ошибка сохранения','errors'=>$query[(int)$materialNumber]->errors];
            }
        }else{
            return ['status'=>'error','errorText'=>'Номер материала больше его количества'];
        }
    }
}
