<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZPublishfile
 *
 * @author Александр
 */
use Yii;
use app\components\MyHelplers;

class ZPublishfile extends ZActionFileWorks {

    public function run() {
        $tmpName = Yii::$app->request->post('tmpName');
        $idZipResorch = Yii::$app->request->post('idZipResorch', false);
        $asJPG = Yii::$app->request->post('asJPG');
        if ($idZipResorch !== false && is_numeric($idZipResorch) && $this->postID && !$tmpName) {
            return MyHelplers::zipPublishFileNew($idZipResorch, $this->postID, $asJPG);
        } elseif (($tmpName) && ($idZipResorch !== false)) {
            $path = $this->createTempPathAndCheckFolderExist($tmpName) . '/files';
            if (is_dir($path)) {
                $list = yii\helpers\FileHelper::findFiles($path, ['recursive' => false]);
                $tmpName = explode('_', $idZipResorch, 2);
                if ($tmpName[0] === 'temp' && count($tmpName) > 1 && is_numeric($tmpName[1])) {
                    $key = (int) $tmpName[1];
                    if (isset($list[$key])) {
                        setlocale(LC_ALL, 'ru_RU.utf8');
                        $fDetail = pathinfo($list[$key]);
                        Yii::debug('Publishfile');
                        Yii::debug([
                            'path'=>$paht,
                            'list'=>$list,
                            'tmoName'=>$tmpName,
                            'fDetail'=>$fDetail,  
                        ]);
                        $publish = $this->createPublishFileName($fDetail['filename'], $fDetail['extension']);
                        if (!is_file($publish[0]))
                            symlink($list[$key], $publish[0]);
                        return ['status' => 'ok', 'newPath' => $publish[0], 'url' => $publish[1], 'ext' => $fDetail['extension']];
                    } else {
                        return ['status' => 'error', 'errorText' => "2 Файл '$idZipResorch' не найден! "];
                    }
                } else {
                    return ['status' => 'error', 'errorText' => "1 Файл '$idZipResorch' не найден! "];
                }
            } else {
                return ['status' => 'error', 'errorText' => "Файл '$path' не найден!"];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Неверные параметры', 'post' => Yii::$app->request->post()];
        }
    }

}
