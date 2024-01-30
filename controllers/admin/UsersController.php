<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\admin;

/**
 * Description of UsersController
 *
 * @author alex
 */
use app\controllers\ControllerMain;
use \app\controllers\ControllerTait;
use yii\filters\VerbFilter;
use app\models\TblUser;
use yii\data\ActiveDataProvider;

class UsersController extends ControllerMain{
    use ControllerTait;
    public function init(){
        parent::init();
        $this->viewPath='@app/views/admin/users';
        $this->layout='main_2.php';
    }
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list'=>['get','post']
                ],
                
            ],
        ];
    }
    public function beforeAction($action) {
        if (parent::beforeAction($action)){
            $this->view ->assetManager
                        ->getBundle('\app\assets\AppAssetUsers')
                        ->register($this->view);
            return true;
        } else {
            return false;
        }
    }
    public function actionList(){
        $query= TblUser::find();
        $provider=new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>[
                'pageSize'=>15
            ]
        ]);
        return $this->render('list',['dataProvider'=>$provider]);
    }
}
