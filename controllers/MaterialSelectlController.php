<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of MaterialController
 *
 * @author Александр
 */
use Yii;
use app\controllers\ControllerMain;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\MyHelplers;
use yii\filters\VerbFilter;
use app\models\tables\Tablestypes;
use app\models\tables\DependTables;
use \app\models\tables\DependTable;
use app\models\tables\MaterialsOnFirms;
use app\models\zakaz\ZakazMaterials;
use \yii\helpers\Html;

class MaterialSelectlController extends ControllerMain {
    use ControllerTait;
    private $_likeFore=[];
    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list' => ['post'],
                ],
            ],
        ];
    }

    private function prepareLikeString($str){
        $tmp=[];
        mb_eregi('/\s+/',$str,$tmp);
        return $tmp;
    }
    public function actionList() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $t=$this->prepareLikeString(Yii::$app->request->post('inputDate',''));

        return [
            'stauts'=>'ok',
            'splt'=>$t
        ];
    }

}
