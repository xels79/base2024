<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;
use Yii;
use app\models\Mail;
/**
 * Description of ZMailFile
 *
 * @author Александр
 */
class ZMailFile extends ZActionFileWorks{
    private function prepareData($idZipResorch,$id,$tmpName){
        if (is_numeric($id)){
            if (is_numeric($idZipResorch)){
                return $this->getFileFromZip($id,$idZipResorch, false);
            }else if(is_string($idZipResorch)&&$tmpName){
                return $this->getFileFromTemDir($idZipResorch, $tmpName, false);
            }else{
                Yii::$app->response->format= \yii\web\Response::FORMAT_HTML;
                throw new \yii\web\BadRequestHttpException('Неверные параметры!');
            }
        }else{
            Yii::$app->response->format= \yii\web\Response::FORMAT_HTML;
            throw new \yii\web\BadRequestHttpException('Не передан id заказа');
        }        
    }
    public function run($idZipResorch=false,$id=null,$tmpName=null){
            $model=new Mail;
            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                if ($model->sendEmail($this->prepareData($idZipResorch,$id,$tmpName))){
                    return ['status'=>'is send'];
                }else{
                    return ['status'=>'error','errorText'=>'Ошибка отправки сообщения'];
                }
            }else{
                if (!$model->fromEmail){
                    $model->fromEmail='zakaz@asterionspb.ru';
                }
                return ['status'=>'edit','html'=>$this->controller->renderPartial('mailform',['model'=>$model, 'id'=>$id, 'idZipResorch'=>$idZipResorch, 'tmpName'=>$tmpName])];
            }

    }
}
