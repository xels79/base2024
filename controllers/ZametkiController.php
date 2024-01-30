<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of ZametkiController
 *
 * @author Александр
 */
use Yii;
use \app\controllers\ControllerTait;
use app\components\MyHelplers;
use app\models\admin\RekvizitOur;
use yii\filters\VerbFilter;
use app\models\Mail;
use app\models\Zametki;
use app\models\ZametkiTabs;

class ZametkiController extends ControllerMain {

    use ControllerTait;

    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'      => ['post'],
                    'save'      => ['post'],
                    'remove'    => ['post'],
                    'getfile'   => ['post'],
                    'send'      => ['post'],
                    'addtab'    => ['post'],
                    'removetab' => ['post'],
                    'renametab' => ['post']
                ]
            ]
        ];
    }

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return true; // or false to not run the action
    }

    public function actionRenametab() {
        if (!$firmKey = Yii::$app->request->post('firmKey')) {
            return ['status' => 'error', 'errorText' => 'Не передан ключ вкладки'];
        }
        if (!$name = Yii::$app->request->post('name')) {
            return ['status' => 'error', 'errorText' => 'Не передано новое название'];
        }
        $id = (int) Yii::$app->request->post('id');

        if (!$id) {
            if (!$model = ZametkiTabs::findOne($firmKey)) {
                return ['status' => 'error', 'errorText' => "Вкладка №$index не найдена"];
            }
        } else {
            if (!$model = Zametki::find()->where(['id' => (int) $id, 'tabId' => (int) $firmKey])->one()) {
                return ['status' => 'error', 'errorText' => "Заметка №$id вкладка №$firmKey не найдена"];
            }
        }
        $model->name = $name;
        if ($model->save()) {
            return ['status' => 'ok'];
        } else {
            return ['status' => 'error', 'errorText' => $model->firstErrors[array_keys($model->errors)[0]]];
        }
    }

    public function actionGetfile() {
        if ($id = Yii::$app->request->post('id')) {
            if ($model = Zametki::findOne($id)) {
                return ['status' => 'ok', 'content' => \yii\helpers\Html::decode($model->content)];
            } else {
                return ['status' => 'error', 'errorText' => "Заметка №$id не найдена"];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передан id заметки'];
        }
    }

    public function actionSave() {
        $tabKey = (int) Yii::$app->request->post('tabKey');
        $zametkaName = (string) Yii::$app->request->post('zametkaName');
        $zametkaContent = Yii::$app->request->post('zametkaContent');
        $id = (int) Yii::$app->request->post('id');
        $content = (string) Yii::$app->request->post('zametkaContent', '');
        if (!$id) {
            if ($zametkaName) {
                $model = new Zametki();
                $model->tabId = $tabKey;
                $model->name = $zametkaName;
                $model->size = mb_strlen($content);
                $model->content = \yii\helpers\Html::encode($content);
            } else {
                return ['status' => 'error', 'errorText' => 'Не указано имя заметки'];
            }
        } else {
            if ($model = Zametki::findOne($id)) {
                $model->size = mb_strlen($content);
                $model->content = \yii\helpers\Html::encode($content);
            } else {
                return ['status' => 'error', 'errorText' => "Запись #$id не найдена!"];
            }
        }
        if ($model->save()) {
            return ['status' => 'ok', 'id' => (int) $model->id];
        } else {
            return ['status' => 'error', 'errorText' => $model->firstErrors[array_keys($model->errors)[0]]];
        }
    }

    public function actionList() {
        $rVal = ['status' => 'ok', 'firms' => []];
        $result = ZametkiTabs::find()
                ->with('zametki')
                ->asArray()
                ->all();
        Yii::debug($result, 'zametki');
        foreach ($result as $tab) {
            $item = [
                'id'    => $tab['id'],
                'name'  => $tab['name'],
                'items' => []
            ];
            foreach ($tab['zametki'] as $zametka) {
                $item['items'][] = [
                    'add_time'         => $zametka['add_time'],
                    'add_temi_text'    => Yii::$app->formatter->asDatetime($zametka['add_time']),
                    'update_time'      => $zametka['update_time'],
                    'update_time_text' => Yii::$app->formatter->asDatetime($zametka['update_time']),
                    'name'             => $zametka['name'],
                    'size'             => $zametka['size'],
                    'id'               => $zametka['id']
                ];
            }
            $rVal['firms'][] = $item;
        }
        return $rVal;
    }

    public function actionAddtab() {
        if ($tabName = Yii::$app->request->post('tabName')) {
            $model = new ZametkiTabs();
            $model->name = $tabName;
            if ($model->save()) {
                return ['status' => 'ok', 'page' => $model->id];
            } else {
                return ['status' => 'error', 'errorText' => $model->firstErrors[array_keys($model->errors)[0]]];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не переданно название вкладки'];
        }
    }

    public function actionRemove() {
        if (!$firmKey = Yii::$app->request->post('firmKey')) {
            return ['status' => 'error', 'errorText' => 'Не передан ключ фирмы'];
        }
        if (!$index = Yii::$app->request->post('index')) {
            return ['status' => 'error', 'errorText' => 'Не передан ключ заметки'];
        }
        if ($model = Zametki::find()->where(['id' => $index, 'tabId' => $firmKey])->one()) {
            $model->delete();
            return ['status' => 'ok'];
        } else {
            return ['status' => 'error', 'errorText' => "Заметка №$index не найдена"];
        }
    }

    public function actionRemovetab() {
        if (!$firmKey = Yii::$app->request->post('firmKey')) {
            return ['status' => 'error', 'errorText' => 'Не передан ключ вкладки'];
        }
        if ($model = ZametkiTabs::findOne($firmKey)) {
            $model->delete();
            return ['status' => 'ok'];
        } else {
            return ['status' => 'error', 'errorText' => "Вкладка №$firmKey не найдена"];
        }
    }

}
