<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZGetstored
 *
 * @author Александр
 */
use Yii;
use yii\helpers\Json;
use app\components\MyHelplers;
use app\models\zakaz\ZakazDirt;
class ZGetstored extends ZActionFileWorks{
    public function run(){
        if ($tmpName=Yii::$app->request->post('tmpName')){
            $path=$this->createTempPathAndCheckFolderExist($tmpName).'/content/content.json';
            if (file_exists($path)){
                $zak=Json::decode(file_get_contents($path));
                return [
                    'zakaz'=>$zak,
                    'files'=>MyHelplers::zipListByPath($this->createTempPathAndCheckFolderExist($tmpName).'/files'),
                    'status'=>'ok'];
            }
        }elseif($dirtId=Yii::$app->request->post('dirtId')){
            if ($zDirt=ZakazDirt::findOne((int) $dirtId)){
                if (Yii::$app->request->post('saveNow')){
                    $tmp=Json::decode($zDirt->other_date);
                    $zak=new \app\models\zakaz\Zakaz;
                    if ($zak->load(['Zakaz'=>$tmp])){
                        if ($zak->save()){
                            return ['status'=>'ok', 'id'=>$zak->id];
                        }else{
                            return ['status'=>'error', 'errorText'=>'Не удалось сохранить'];
                        }
                    }else{
                        return ['status'=>'error', 'errorText'=>'Не удалось загрузить данные в заказ'];
                    }
                }else{
                    $tmp=Json::decode($zDirt->other_date);
                    //unset ($tmp['zak_id']);
                    unset ($tmp['dateofadmission']);
                    return[
                        'zakaz'=> ['content'=>$tmp],
                        'productCategory'=>$zDirt->productCategory,
                        'productCategory2'=>$zDirt->productCategory2,
                        'status'=>'ok'
                    ];
                }
            }else{
                return ['status'=>'error','errorText'=>'Черновик с id="'.$dirtId.'" не найден.'];
            }
        }else{
            return ['status'=>'error','errorText'=>'Не переданы, или неверные параметры!','post'=>$_POST];
        }
    }
}
