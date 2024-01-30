<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of MCEImageController
 *
 * @author Александр
 */
use Yii;
use \app\controllers\ControllerTait;
use app\components\MyHelplers;
use yii\filters\VerbFilter;
use app\models\TinymceImageLoader;
use yii\web\UploadedFile;

class MceimageController extends ControllerMain {

    use ControllerTait;

    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'save' => ['post'],
                ]
            ]
        ];
    }

    public function actionSave() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $file = UploadedFile::getInstanceByName('file');
        if ($file->saveAs('@app/web/pic/tinymce/' . $file->baseName . '.' . $file->extension)) {
            return ['location' => '/pic/tinymce/' . $file->baseName . '.' . $file->extension];
        } else {
            return [];
        }
    }

}
