<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZGetfile
 *
 * @author Александр
 */
use Yii;
use app\models\Mail;
class ZGetfile extends ZActionFileWorks{
    public function run($idZipResorch=false,$id=null,$tmpName=null){
        if (is_numeric($id)){
            if (is_numeric($idZipResorch)){
                return $this->getFileFromZip($id,$idZipResorch);
            }else if(is_string($idZipResorch)&&$tmpName){
                return $this->getFileFromTemDir($idZipResorch, $tmpName);
            }else{
                Yii::$app->response->format= \yii\web\Response::FORMAT_HTML;
                throw new \yii\web\BadRequestHttpException('Неверные параметры!');
            }
        }else{
            Yii::$app->response->format= \yii\web\Response::FORMAT_HTML;
            throw new \yii\web\BadRequestHttpException('Не передан id заказа');
        }
    }
}
