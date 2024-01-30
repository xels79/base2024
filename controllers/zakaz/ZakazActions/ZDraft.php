<?php

/*
 * Это файл создан xel_s
 * Для проекта СУБД ver2  * 
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZDraft
 *
 * @author xel_s
 */
use Yii;
use app\models\zakaz\Zakaz;
use app\models\zakaz\ZakazDirt;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class ZDraft extends ZActionAEBase{
    public function run(){
        $rVal= ['status'=>'saved','post'=>Yii::$app->request->post()];
        $zak=new Zakaz();
//        $zak->stage=0;
//        $zak->division_of_work=1;
//        $zak->method_of_payment=0;
//        $zak->production_id=1;
//        $zak->dateofadmission=Yii::$app->formatter->asDate(time());
//        $zak->ourmanager_id=Yii::$app->user->identity->id;
        if ($zak->load(Yii::$app->request->post(),'Zakaz')){
            $z_arr=$zak->toArray();//[],['materials','podryad']);
            $z_arr['stage']=0;
            $z_arr['division_of_work']=1;
            $z_arr['method_of_payment']=1;
            $z_arr['invoice_from_this_company']=0;
            $z_arr['account_number']='';
            $z_arr['is_express']=0;
//            $z_arr['production_id']=1;
            $z_arr['dateofadmission']=Yii::$app->formatter->asDate(time());
            $z_arr['ourmanager_id']=Yii::$app->user->identity->id;

            $z_arr['materials']= ArrayHelper::getValue(Yii::$app->request->post('Zakaz',[]),'materials',[]);
            $z_arr['podryad']=ArrayHelper::getValue(Yii::$app->request->post('Zakaz',[]),'podryad',[]);
            $z_arr['post_print']=ArrayHelper::getValue(Yii::$app->request->post('Zakaz',[]),'post_print',[]);
            for($i=0;$i<count($z_arr['materials']);$i++){
                unset($z_arr['materials'][$i]['id']);
                $z_arr['materials'][$i]['zakaz_id']=null;
                $z_arr['materials'][$i]['order_date']=null;
                $z_arr['materials'][$i]['delivery_date']=null;
                $z_arr['materials'][$i]['paid']=0;
            }
            for($i=0;$i<count($z_arr['podryad']);$i++){
                unset($z_arr['podryad'][$i]['id']);
                $z_arr['podryad'][$i]['zakaz_id']=null;
                $z_arr['podryad'][$i]['date_info ']=null;
                $z_arr['podryad'][$i]['paid']=0;
            }
            $dirt=new ZakazDirt();
            if (array_key_exists('id', $z_arr)) unset($z_arr['id']);
            $dirt->setAttributes($z_arr);
            $dirt->other_date=Json::encode($z_arr);
            $rVal['ZakazDirt']=$dirt->toArray();
            $rVal['Zakaz']=$z_arr;
            if ($dirt->save()){
                if ($tmpName=ArrayHelper::getValue(Yii::$app->request->post('Zakaz'), 'tmpName')) $this->controller->doCansel($tmpName);
                if ($tmpMenu=$this->controller->showMessage(true)){
                    $rVal['label']=$tmpMenu['label'];
                    $rVal['menu']= \yii\bootstrap\Dropdown::widget(['items'=>$tmpMenu['items'],'options'=>['class'=>'nav-mess-main']]);

                }
                return $rVal;
            }else{
            return [
                'status'=>'error',
                'errors'=>$dirt->errors
            ];
                
            }
        }else
            return [
                'status'=>'error',
                'errorText'=>'Не переданны данные заказа'
            ];
    }
}
