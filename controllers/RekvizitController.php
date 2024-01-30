<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of RekvizitController
 *
 * @author Александр
 */
use Yii;
use \app\controllers\ControllerTait;
use app\components\MyHelplers;
use app\models\admin\RekvizitOur;
use yii\filters\VerbFilter;
use app\models\Mail;
use app\models\DocumentFiles;

class RekvizitController extends ControllerMain {

    use ControllerTait;

    public $fNamePerfix = 'doc';

    public function init() {
        parent::init();
        $this->fNamePerfix = Yii::$app->request->get('fNamePerfix', Yii::$app->request->post('fNamePerfix', $this->fNamePerfix));
    }

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        MyHelplers::checkDirToExistAndCreate(\Yii::getAlias('@documents'));
        setlocale(LC_ALL, 'ru_RU.utf8');
        return true;
    }

    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'      => ['post'],
                    'save'      => ['post'],
                    'remove'    => ['post'],
                    'getfile'   => ['get'],
                    'send'      => ['post'],
                    'addtab'    => ['post'],
                    'removetab' => ['post']
                ]
            ]
        ];
    }

    private function createPathToDir() {
        MyHelplers::checkDirToExistAndCreate(\Yii::getAlias('@documents'));
        return \Yii::getAlias('@documents/');
    }

    private function firmFileName($id, $name) {
        MyHelplers::checkDirToExistAndCreate(\Yii::getAlias('@documents'));
        return \Yii::getAlias('@documents/' . $id . $this->fNamePerfix . '_' . $name . '.zip');
    }

    private function readZip($path) {
        $zip = New \ZipArchive;
        $res = $zip->open($path);
        $rVal = [];
        if ($res === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $el = $zip->statIndex($i);
                $rVal[$i] = [
                    'name'          => $el['name'],
                    'size'          => $el['size'],
                    'add_time'      => $el['mtime'],
                    'add_time_text' => Yii::$app->formatter->asDatetime($el['mtime'])
                ];
            }
        }
        return $rVal;
    }

    private function zipGetContent(int $firmKey, int $index, string $name) {
        $zipPath = $this->firmFileName($firmKey, $name);
        if (file_exists($zipPath)) {
            $zip = New \ZipArchive;
            $res = $zip->open($zipPath);
            if ($res === true) {
                setlocale(LC_ALL, 'ru_RU.utf8');
                return ['content' => $zip->getFromIndex($index), 'fDetail' => pathinfo($zip->getNameIndex($index))];
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                throw new \Exception('Ошибка архива: ' . $res);
            }
        } else {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            throw new \Exception('Архив не найден ' . $zipPath);
        }
    }

    public function actionGetfile(int $firmKey, int $index) {
        if ($this->fNamePerfix === 'doc') {
            $mClass = RekvizitOur::className();
        } else {
            $mClass = new DocumentFiles(['fNamePerfix' => $this->fNamePerfix, 'basePath' => '@documents']);
        }

        if ($firm = $mClass::findOne((int) $firmKey)) {
            $tmp = $this->zipGetContent($firmKey, $index, $firm->name);
            Yii::debug($tmp, 'getFile');
//            return Yii::$app->response->sendContentAsFile(\yii\helpers\VarDumper::dumpAsString($tmp['fDetail']), 'test.txt',['mimeType'=>MyHelplers::$mime_types['txt']]);
            return Yii::$app->response->sendContentAsFile($tmp['content'], \yii\helpers\Html::encode($tmp['fDetail']['filename'] . '.' . $tmp['fDetail']['extension']), ['mimeType' => MyHelplers::$mime_types[$tmp['fDetail']['extension']]]);
        } else {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            throw new \Exception('Фирма не найдена');
        }
    }

    public function actionRemovetab() {
        if (!$firmKey = Yii::$app->request->post('firmKey')) {
            return ['status' => 'error', 'errorText' => 'Не передан ключ фирмы'];
        }
        if ($this->fNamePerfix === 'doc') {
            $mClass = RekvizitOur::className();
        } else {
            $mClass = new DocumentFiles(['fNamePerfix' => $this->fNamePerfix, 'basePath' => '@documents']);
        }
        if ($firm = $mClass::findOne((int) $firmKey)) {
            $zipPath = $this->firmFileName($firm->rekvizit_id, $firm->name);
            if (file_exists($zipPath)) {
                unlink($zipPath);
                return ['status' => 'ok'];
            } else {
                return ['status' => 'error', 'errorText' => 'Файл вкладки не найден'];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Вкладка или фирма не найдена'];
        }
    }

    public function actionRemove() {
        if (!$firmKey = Yii::$app->request->post('firmKey')) {
            return ['status' => 'error', 'errorText' => 'Не передан ключ фирмы'];
        }
        $index = Yii::$app->request->post('index');
        if ($index === null) {
            return ['status' => 'error', 'errorText' => 'Не передан индекс файла'];
        }
        if ($this->fNamePerfix === 'doc') {
            $mClass = RekvizitOur::className();
        } else {
            $mClass = new DocumentFiles(['fNamePerfix' => $this->fNamePerfix, 'basePath' => '@documents']);
        }
        if ($firm = $mClass::findOne((int) $firmKey)) {
            $zipPath = $this->firmFileName($firm->rekvizit_id, $firm->name);
            Yii::debug($zipPath, 'actionRemove');
            if (file_exists($zipPath)) {
                $zip = New \ZipArchive;
                $res = $zip->open($zipPath);
                if ($res === true) {
                    if ($zip->deleteIndex((int) $index) === true) {
                        return ['status' => 'ok'];
                    } else {
                        return ['status' => 'error', 'errorText' => 'Ошибка удаления'];
                    }
                } else {
                    return ['status' => 'error', 'errorText' => 'Ошибка архива: ' . $res];
                }
            } else {
                return ['status' => 'error', 'errorText' => 'Архив не найден'];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Фирма не найдена'];
        }
    }

    public function actionSave() {
        if (!array_key_exists('add_file', $_FILES)) {
            return ['status' => 'error', 'errorText' => 'Файл не передан', 'files' => $_FILES];
        }
        //DocumentFiles
        if ($this->fNamePerfix === 'doc') {
            $mClass = RekvizitOur::className();
        } else {
            $mClass = new DocumentFiles(['fNamePerfix' => $this->fNamePerfix, 'basePath' => '@documents']);
        }
        if ($firm = $mClass::findOne((int) Yii::$app->request->post('firmId'))) {
            $zipPath = $this->firmFileName($firm->rekvizit_id, $firm->name);
            \Yii::debug([$firm->toArray(), $zipPath], 'DocumentFiles');
            $zip = New \ZipArchive;
            $res = $zip->open($zipPath, \ZipArchive::CREATE);
            if ($res === true) {
                if ($zip->addFile($_FILES['add_file']['tmp_name'], $_FILES['add_file']['name'])) {
                    return ['status' => 'ok'];
                } else {
                    return ['status' => 'error', 'errorText' => 'Ошибка добавления файла.'];
                }
            } else {
                return ['status' => 'error', 'errorText' => 'Ошибка архива: ' . $res];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Фирма не найдена', 'post' => $_POST];
        }
        return ['status' => 'ok'];
    }

    public function actionList() {
        $rVal = ['status' => 'ok', 'firms' => []];
        if ($this->fNamePerfix === 'doc') {
            foreach (RekvizitOur::find()->select('rekvizit_id as id,name')->asArray()->all() as $el) {
                $path = $this->firmFileName($el['id'], $el['name']);
                if (file_exists($path)) {
                    $rVal['firms'][] = ['id' => $el['id'], 'name' => $el['name'], 'items' => $this->readZip($path)];
                } else {
                    $rVal['firms'][] = ['id' => $el['id'], 'name' => $el['name'], 'items' => []];
                }
            }
        } else {
            $path = $this->createPathToDir();
            setlocale(LC_ALL, 'ru_RU.utf8');
            if ($handle = opendir($path)) {
                $rVal['tmp'] = [];
                while (false !== ($entry = readdir($handle))) {
                    if (is_file($path . $entry)) {
                        if (count($tmp = explode($this->fNamePerfix . '_', $entry)) > 1) {
                            if (!array_key_exists('firms', $rVal))
                                $rVal['firms'] = [];
                            if (!array_key_exists((int) $tmp[0] - 1, $rVal['firms']))
                                $rVal['firms'][(int) $tmp[0] - 1] = ['id' => $tmp[0], 'name' => pathinfo($tmp[1], PATHINFO_FILENAME), 'items' => $this->readZip($path . $entry)];
                            else
                                array_merge($rVal['firms'][(int) $tmp[0] - 1]['items'], $this->readZip($path . $entry));
                            $rVal['tmp'][] = ['fNamePerfix' => $this->fNamePerfix . '_', '$entry' => $entry, 'explode' => $tmp, 'pInfo' => pathinfo($tmp[1], PATHINFO_FILENAME)];
                        }
                    }
                }
            }
        }
        return $rVal;
    }

    public function actionSend($firmKey = null, $index = null) {
        if ($firmKey === null) {
            if (!$firmKey = (int) Yii::$app->request->post('firmKey')) {
                return ['status' => 'error', 'errorText' => 'Не передан ключ фирмы'];
            }
        }
        if ($index === null) {
            $index = Yii::$app->request->post('index');
            if ($index === null) {
                return ['status' => 'error', 'errorText' => 'Не передан индекс файла'];
            }
        }
        if ($this->fNamePerfix === 'doc') {
            $mClass = RekvizitOur::className();
        } else {
            $mClass = new DocumentFiles(['fNamePerfix' => $this->fNamePerfix, 'basePath' => '@documents']);
        }

        if ($firm = $mClass::findOne((int) $firmKey)) {
            $model = new Mail;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->sendEmail($this->zipGetContent($firmKey, $index, $firm->name))) {
                    return ['status' => 'is send'];
                } else {
                    return ['status' => 'error', 'errorText' => 'Ошибка отправки сообщения'];
                }
            } else {
                if (!$model->fromEmail) {
                    $model->fromEmail = 'zakaz@asterionspb.ru';
                }
                return ['status' => 'edit', 'html' => $this->renderPartial('mailform', ['model' => $model, 'firmKey' => $firmKey, 'index' => (int) $index])];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Фирма не найдена'];
        }
    }

    public function actionAddtab() {
        if ($tabName = Yii::$app->request->post('tabName')) {
            $path = $this->createPathToDir();
            if ($handle = opendir($path)) {
                $next = 1;
                while (false !== ($entry = readdir($handle))) {
                    if (is_file($path . $entry)) {
                        if (count($tmp = explode($this->fNamePerfix . '_', $entry)) > 1) {
                            if ((int) $tmp[0] >= $next) {
                                $next = (int) $tmp[0] + 1;
                            }
                        }
                    }
                }
                $zip = new \ZipArchive;
                $res = $zip->open($path . $next . $this->fNamePerfix . '_' . $tabName . '.zip', \ZipArchive::CREATE);
                //$res = $zip->open($this->firmFileName($next, $tabName, \ZipArchive::CREATE));
                if ($res === TRUE) {
                    $zip->addFromString('NOTREAD', 'Этот файл необходим при создание вкладки');
//                    $zip->addEmptyDir('NOTREAD');
                    $zip->close();
                    return ['status' => 'ok', 'page' => $next, 'fPath' => $path . $next . $this->fNamePerfix . '_' . $tabName . '.zip'];
                } else {
                    return ['status' => 'error', 'errorText' => $zip->getStatusString()];
                }
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передано название вкладки'];
        }
    }

}
