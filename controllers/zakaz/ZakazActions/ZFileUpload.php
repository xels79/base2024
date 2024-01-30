<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZFileUpload
 *
 * @author Александр
 */
use Yii;
use yii\web\UploadedFile;
class ZFileUpload extends ZActionFileWorks{
    public function run(){
        $id=Yii::$app->request->post('id');
        $isDes=Yii::$app->request->post('isDes');
        $perfix=$isDes==='true'?'des_':'main_';
        if (!$tmpName=Yii::$app->request->post('tmpName')){
            return ['status'=>'error','errorText'=>'Не переданно имя временного католога!','post'=>Yii::$app->request->post()];
        }
        $tempSubF=$this->createTempPathAndCheckFolderExist($tmpName);
        if (!$id){
            $file=UploadedFile::getInstanceByName('file');
            if ($file->saveAs($tempSubF.'/files/'.$perfix.$file->baseName.'.'.$file->extension)){
                return ['status'=>'ok','isDes'=>$isDes,'$perfix'=>$perfix,'post'=>$_POST];
            }else{
                return ['status'=>'error','errorText'=>$file->error,'tempSubF'=>$tempSubF,'tempName'=>$tmpName];
            }
        }else{
        }
        return ['post'=>Yii::$app->request->post(),'files'=>$_FILES,'sunF'=> ''];
    }
}
