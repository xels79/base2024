<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZGetfullmaterialinfo
 *
 * @author Александр
 */
use Yii;
class ZGetfullmaterialinfo extends ZAction{
    public function run(){
        if ($tmp=Yii::$app->request->post('value_lists')){
            $rVal=['status'=>'ok','list'=>[]];
            foreach($tmp as $val){
                if ($mat= \app\models\tables\Tablestypes::findOne($val['type_id'])){
                    $rVal['list'][]=$this->_materialInfoByContent($mat,$val);
                }else{
                    $rVal['list'][]='Не найден';
                }
            }
            return $rVal;
        }elseif($tmp=Yii::$app->request->post('values')){
            return $this->materialInfoByContent($tmp);
        }else{
            return['status'=>'error','errorText'=>'Getfullmaterialinfo: Не пераданны параметры!'];
        }
    }
}
