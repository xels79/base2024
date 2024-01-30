<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZActionFileWorks
 *
 * @author Александр
 */
use Yii;
use yii\helpers\FileHelper;
use app\components\MyHelplers;

class ZActionFileWorks extends ZAction {

    protected function getFileFromZip($id, $idZipResorch, $send = true) {
        $zipPath = MyHelplers::zipPathToStore($id);
        if (file_exists($zipPath)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                setlocale(LC_ALL, 'ru_RU.utf8');
                $fDetail = pathinfo($zip->getNameIndex($idZipResorch));
                $fName = MyHelplers::formatName($id) . '_' . explode('_', $fDetail['basename'], 2)[1];
                if ($send)
                    return Yii::$app->response->sendContentAsFile($zip->getFromIndex($idZipResorch), $fName, ['mimeType' => MyHelplers::$mime_types[$fDetail['extension']]]);
                else
                    return ['content' => $zip->getFromIndex($idZipResorch), 'fDetail' => $fDetail];
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
                throw new \yii\web\BadRequestHttpException("Не удается открыть файл $zipPath : " . $zip->getStatusString());
            }
        } else {
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            throw new \yii\web\NotFoundHttpException("Файд '$zipPath' не найден!");
        }
    }

    protected function createPublishFileName($name, $ext) {
        return MyHelplers::createPublishFileName($name, $ext);
    }

    protected function getFileFromTemDir($id_file, $tmpName, $send = true) {
        $path = $this->createTempPathAndCheckFolderExist($tmpName) . '/files';
        if (is_dir($path)) {
            $list = yii\helpers\FileHelper::findFiles($path, ['recursive' => false]);
            $tmpName = explode('_', $id_file, 2);
            if ($tmpName[0] === 'temp' && count($tmpName) > 1 && is_numeric($tmpName[1])) {
                $key = (int) $tmpName[1];
                if (isset($list[$key])) {
                    setlocale(LC_ALL, 'ru_RU.utf8');
                    $fDetail = pathinfo($list[$key]);
                    if ($send)
                        return Yii::$app->response->sendFile($list[$key], $fDetail['basename'], ['mimeType' => MyHelplers::$mime_types[$fDetail['extension']]]);
                    else
                        return ['content' => $zip->getFromIndex($idZipResorch), 'fDetail' => $fDetail];
                } else {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
                    throw new \yii\web\NotFoundHttpException("2 Файл '$id_file' не найден! ");
                }
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
                throw new \yii\web\NotFoundHttpException("1 Файл '$id_file' не найден! " . Json::encode($tmpName) . "" . \yii\helpers\VarDumper::dumpAsString(is_numeric($tmpName[1])));
            }
        } else {
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            throw new \yii\web\NotFoundHttpException("Файл '$path' не найден!");
        }
    }

}
