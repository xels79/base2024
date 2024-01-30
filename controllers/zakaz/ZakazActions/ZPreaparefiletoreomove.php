<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZPreaparefiletoreomove
 *
 * @author Александр
 */
use Yii;
class ZPreaparefiletoreomove extends ZActionFileWorks{
    public function run(){
        if ($this->postID&&($tmpName=Yii::$app->request->post('tmpName'))&&($idZipResorch=Yii::$app->request->post('idZipResorch',false))!==false){
            $tempSubF=$this->pathToRemoveFileStored($tmpName);
            $val=$this->toRemoveFileStored($tmpName);
            if (!isset($val[$this->postID])) $val[$this->postID]=[];
            $val[$this->postID][]=$idZipResorch;
            $out= fopen($tempSubF, 'w');
            fwrite($out, \yii\helpers\Json::encode($val));
            fclose($out);
            return ['status'=>'ok','toRem'=>$val];
        }else{
            return ['status'=>'error','errorText'=>'Не переданы, или неверные параметры!','post'=>$_POST];
        }
    }
}
