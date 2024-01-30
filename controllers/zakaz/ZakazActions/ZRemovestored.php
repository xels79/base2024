<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZRemovestored
 *
 * @author Александр
 */
use Yii;
use yii\helpers\FileHelper;
use app\components\MyHelplers;
class ZRemovestored extends ZActionFileWorks{
    public function run(){
        if ($tmpName=Yii::$app->request->post('tmpName')){
            $rVal=['status'=>'ok'];
            $path=$this->createTempPathAndCheckFolderExist($tmpName);
            if ($tmpName!=='ALL'){
                if (file_exists($path)){
                    FileHelper::removeDirectory($path);
                    $rVal['mess']='Удален каталог "'.$tmpName.'".';
                }else{
                    $rVal['mess']='Каталог "'.$tmpName.'" не найден.';
                }
            }else{
                $path=Yii::getAlias('@temp/'.MyHelplers::translit(Yii::$app->user->identity->realname));
                if (file_exists($path)){
                    FileHelper::removeDirectory($path);
                    $rVal['mess']='Удален каталог "'.MyHelplers::translit(Yii::$app->user->identity->realname).'".';
                }else{
                    $rVal['mess']='Каталог "'.MyHelplers::translit(Yii::$app->user->identity->realname).'" не найден.';
                }
            }
            if ($tmpMenu=$this->controller->showMessage(true)){
                $rVal['label']=$tmpMenu['label'];
                $rVal['menu']= \yii\bootstrap\Dropdown::widget(['items'=>$tmpMenu['items'],'options'=>['class'=>'nav-mess-main']]);

            }
            return $rVal;
        }else{
            return ['status'=>'error','errorText'=>'Не переданы, или неверные параметры!','post'=>$_POST];
        }

    }
}
