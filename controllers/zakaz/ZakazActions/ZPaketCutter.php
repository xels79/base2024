<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;
use Yii;
use app\controllers\zakaz\ZakazActions\ZLiffCutter;
use app\widgets\LiffCutter\Paket;
/**
 * Description of ZPaketCutter
 *
 * @author Александр
 */
class ZPaketCutter extends ZLiffCutter{
    public function init(){
        $wP=(int)Yii::$app->request->post('wP',0);
        $hP=(int)Yii::$app->request->post('hP',0);
        $zP=(int)Yii::$app->request->post('zP',0);
        $lam=Yii::$app->request->post('lam',0)==='true'?true:false;
        if (!$wP||!$hP||!$zP){
            $this->errorContent= ['error'=>true,'errorText'=>"Неправильные размеры пакета '"+$wP+"*"+$hP+"*"+$zP+"'",'errorHead'=>'Ошибка данных'];
            $this->hasError=true;
            return;
        }
        $this->calkBumFormat();
        $paket=new Paket($this->bumformat[0],$this->bumformat[1],$wP,$hP,$zP,$lam);
        $paketAns=$paket->run();
        $this->format_printing_block=[$paketAns['wLifN'],$paketAns['hLifN']];
        $this->pW=300;
        $this->mm=true;
        parent::init();
        if (!$this->hasError){
            $this->retValue=['paket'=>$paketAns,'liffCuter'=>$this->retValue];
        }
    }
}
