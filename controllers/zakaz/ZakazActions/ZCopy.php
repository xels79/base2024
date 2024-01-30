<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZCopy
 *
 * @author Александр
 */
use Yii;
use app\models\zakaz\Zakaz;
use app\models\zakaz\ZakazMaterials;
use app\models\zakaz\ZakazPod;
use app\components\MyHelplers;

class ZCopy extends ZAction {

    private function proceedFiles(&$model, &$zakazNew) {
        $zipPathS = MyHelplers::zipPathToStore($model->id);
        if (file_exists($zipPathS)) {
            $zipS = new \ZipArchive();
            $dirSch = 'Z' . MyHelplers::formatName((string) $model->id, '0', 8);
            if ($zipS->open($zipPathS) === true) {
                $zipPathD = MyHelplers::zipPathToStore($zakazNew->id);
                $dirDest = 'Z' . MyHelplers::formatName((string) $zakazNew->id, '0', 8);
                if ($zipPathS === $zipPathD) {
                    $zipD = $zipS;
                } else {
                    $zipD = new \ZipArchive();
                    if ($zipD->open($zipPathD, \ZipArchive::CREATE) !== true) {
                        return ['status' => 'error', 'errorText' => 'Не воэможно открыть файл - "' . $zipPathD . '"'];
                    }
                }
                $tmp = [];
                $perfiXesStore = [];
                for ($i = 0; $i < $zipS->numFiles; $i++) {
                    $fName = $zipS->getNameIndex($i);
                    setlocale(LC_ALL, 'ru_RU.utf8');
                    $file = pathinfo($fName);
                    $zakazPerfix = explode('/', $file['dirname'], 2)[0];
                    $perfiXesStore[] = $zakazPerfix;
                    $fCnt = 0;
                    if ($zakazPerfix === $dirSch) {
                        $tmp[] = [
                            $fName,
                            'fInfo'   => $file,
                            'newFile' => $dirDest . '/' . $file['basename']
                        ];
                        $zipD->addFromString($dirDest . '/' . $file['basename'], $zipS->getFromName($fName));
                        $fCnt++;
                    }
                }
                if ($zipPathS !== $zipPathD) {
                    $zipD->close();
                }
                $zipS->close();
                return ['status'   => 'ok', 'infoText' => 'Скопировано #' . $fCnt . ' файлов', 'info'     => [
                        '$perfiXesStore' => $perfiXesStore,
                        '$zipPathS'      => $zipPathS,
                        '$dirSch'        => $dirSch,
                        '$zipPathD'      => $zipPathD,
                        '$dirDest'       => $dirDest,
                        'fList'          => $tmp
                ]];
            } else {
                return ['status' => 'error', 'errorText' => 'Не воэможно открыть файл - "' . $zipPathS . '"'];
            }
        }
        return ['status' => 'ok', 'infoText' => 'Файлы не копировались'];
    }

    private function proceedPodryad(&$model, &$zakazNew, $re_print) {
        foreach ($podryad = ZakazPod::find()->where(['zakaz_id' => $model->id])->asArray()->all() as $el) {
            if (array_key_exists('id', $el)) {
                $m = $el['id'];
                unset($el['id']);
            } else {
                $m = -1;
            }
            $tmpM = new ZakazPod();
            $el['zakaz_id'] = $zakazNew->id;
            $el['date_info'] = null;
            $el['paid'] = 0;
            if ($re_print) {
                $el['coast'] = 0;
            }
//            $tmpM->pod_Id
            foreach (array_keys($el) as $k) {
                $tmpM->$k = $el[$k];
            }
            if (!$tmpM->save()) {
                $zakazNew->delete();
                return ['status' => 'error', 'errorText' => "Не удалось скопировать заказ №$this->postID<br>Ошибка сохранения подрядчика #$m", 'errors' => $tmpM->errors];
            }
        }
        return $this->proceedFiles($model, $zakazNew);
    }

    private function proceedMaterial(&$model, &$zakazNew, $re_print) {
        foreach ($materials = ZakazMaterials::find()->where(['zakaz_id' => $model->id])->asArray()->all() as $el) {
            if (array_key_exists('id', $el)) {
                $m = $el['id'];
                unset($el['id']);
            } else {
                $m = -1;
            }
            $el['zakaz_id'] = $zakazNew->id;
            $el['order_date'] = null;
            $el['delivery_date'] = null;
            $el['paid'] = 0;
            $tmpM = new ZakazMaterials();
            foreach (array_keys($el) as $k) {
                $tmpM->$k = $el[$k];
            }
            if (!$tmpM->save()) {
                $zakazNew->delete();
                return ['status' => 'error', 'errorText' => "Не удалось скопировать заказ №$this->postID<br>Ошибка сохранения материала #$m", 'errors' => $tmpM->errors];
            }
        }
        return $this->proceedPodryad($model, $zakazNew, $re_print);
    }

    private function proceedWithModel(&$model, $deadline, $re_print) {
        $z_arr = $model->toArray();
        if (array_key_exists('id', $z_arr))
            unset($z_arr['id']);
        $z_arr['dateofadmission'] = Yii::$app->formatter->asDate(time());
        $z_arr['deadline'] = $deadline;
        $z_arr['materials'] = [];
        $z_arr['podryad'] = [];
        $z_arr['date_of_receipt'] = null;
        $z_arr['date_of_receipt1'] = null;
        $z_arr['stage'] = 0;

        if ($re_print) {
            $z_arr['re_print'] = $re_print;
            $z_arr['material_coast_comerc'] = 0;
            $z_arr['total_coast'] = 0;
            $z_arr['profit'] = 0 - $z_arr['spending'] - $z_arr['spending2'];
        }
        $zakazNew = new Zakaz();
        foreach (array_keys($z_arr) as $k) {
            $zakazNew->$k = $z_arr[$k];
        }
        $zakazNew->ourmanager_id = Yii::$app->user->identity->id;
        $zakazNew->dateofadmission = Yii::$app->formatter->asDate(time());
        if ($zakazNew->save()) {
            return $this->proceedMaterial($model, $zakazNew, $re_print);
        } else {
            return ['status' => 'error', 'errorText' => "Не удалось скопировать заказ №$this->postID", 'errors' => $zakazNew->errors];
        }
    }

    public function run() {
        $re_print = (int) Yii::$app->request->post('re_print', 0);
        if (!$deadline = Yii::$app->request->post('deadline')) {
            return ['status' => 'error', 'errorText' => "Не переданна дата сдачи заказа!"];
        }
        if ($model = Zakaz::findOne($this->postID)) {
            return $this->proceedWithModel($model, $deadline, $re_print);
        } else {
            return ['status' => 'error', 'errorText' => "Заказ №$this->postID - не найден"];
        }
    }

}
