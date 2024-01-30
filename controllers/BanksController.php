<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of BanksController
 *
 * @author Александр
 */
use Yii;
use app\controllers\ControllerMain;
use yii\web\Response;
use rusbankshb\models\Bank;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use \app\controllers\ControllerTait;
class BanksController extends ControllerMain{
    use ControllerTait;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'searchbank'=>['post']
                ],
                
            ],
        ];
    }
    public function actionSearchbank(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q=Yii::$app->request->post('q',null);
        $f=Yii::$app->request->post('f','bik');
        if (!array_key_exists($f, (new Bank())->getAttributes())) {
            return ['status'=>'error','errorText'=>'Нельзя искать по запрашиваемому полю.'];
        }else
            return [
                'status'=>'ok',
                'list'=>ArrayHelper::map(Bank::find()->andFilterWhere(['like', $f, $q])->all(), 'bik', 'attributes')
            ];
    }
}
