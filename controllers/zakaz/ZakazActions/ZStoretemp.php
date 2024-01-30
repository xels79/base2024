<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZStoretemp
 *
 * @author Александр
 */
use Yii;

class ZStoretemp extends ZAction {

    public function run() {
        $tmpInfo = Yii::$app->request->post('Zakaz');
        $speed = (int) Yii::$app->request->post('speed', 6);
        Yii::$app->session->open();
        $timeOut = (int) (!Yii::$app->request->post('reset') ? (Yii::$app->session->getFlash('storeTOut', Yii::$app->user->authTimeout) - $speed) : Yii::$app->user->authTimeout);
        Yii::$app->session->setFlash('storeTOut', $timeOut);
        if ($timeOut <= 0)
            Yii::$app->session->destroy();
        else
            Yii::$app->user->authTimeout = $timeOut;
        if (is_array($tmpInfo)) {
            if (isset($tmpInfo['tmpName'])) {
                $path = $this->createTempPathAndCheckFolderExist($tmpInfo['tmpName']) . '/content';
                if (file_exists($path)) {
                    $out = fopen($path . '/content.json', 'w+');
                    fwrite($out, \yii\helpers\Json::encode([
                                'id'      => $this->postID ? (int) $this->postID : null,
                                'content' => $tmpInfo,
                    ]));
                    fclose($out);
                    return (['status' => 'saved', 'timeout' => $timeOut]);
                }
            }
        }
        return ['status' => 'error', 'errorText' => 'Ошибка сохранения'];
    }

}
