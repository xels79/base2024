<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZCustomerlist
 *
 * @author Александр
 */
use Yii;
use app\models\admin\Zak;
use app\models\admin\ContactZak;
class ZCustomerlist extends ZAction{
    public function run(){
        if (!$this->postID){
            $tmp=Zak::find()->select(['firm_id as id','mainName as name'])->where(['status'=>true])->orderBy('mainName')->asArray()->all();
        }else{
            $tmpZak=Zak::find()->select(['firm_id','mainName as name','blackList'])->where(['firm_id'=>$this->postID])->orderBy('mainName')->asArray()->one();
            $tmp=ContactZak::find()->select(['contactZak_id as id','name'])->where(['firm_id'=>$this->postID])->asArray()->all();
            if ($tmpZak['blackList']){
                return ['status'=>'error','headerText'=>'Внимание! "'.$tmpZak['name'].'" в чёрном списке','errorText'=>$tmpZak['blackList'],'source'=>$this->createForSource($tmp)];
            }
        }
        return ['status'=>'ok','source'=>$this->createForSource($tmp)];
    }
}
