<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz;
use Yii;
use app\controllers\ControllerMain;
/**
 * Description of Zakazbase
 *
 * @author Александр
 */
abstract class Zakazbase extends ControllerMain{
    public $postID=null;
    public function beforeAction($action){
        $rVal=parent::beforeAction($action);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->postID=Yii::$app->request->post('id',null);
        return $rVal;
    }

}
