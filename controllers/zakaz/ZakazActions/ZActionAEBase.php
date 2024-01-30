<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZActionAEBase
 *
 * @author Александр
 */

namespace app\controllers\zakaz\ZakazActions;

use Yii;
use yii\helpers\FileHelper;
use app\components\MyHelplers;
use app\models\admin\Pod;
use yii\helpers\ArrayHelper;
use app\models\zakaz\ZakazOplata;
use app\models\tables\MaterialsOnFirms;

class ZActionAEBase extends ZAction {

    private function copyProceeedRePrint(&$z_arr, $re_print) {
        $z_arr['values']['re_print'] = $re_print;
        $z_arr['values']['material_coast_comerc'] = 0;
        $z_arr['values']['total_coast'] = 0;
        $z_arr['values']['profit'] = 0 - $z_arr['values']['spending'] - $z_arr['values']['spending2'];
        if (is_array($z_arr['values']['podryad'])) {
            for ($i = 0; $i < count($z_arr['values']['podryad']); $i++) {
                $z_arr['values']['podryad'][$i]['coast'] = 0;
            }
        }
    }

    private function copyProceeed(&$z_arr, $re_print) {
        $z_arr['values']['ourmanager_id'] = Yii::$app->user->identity->id;
        $z_arr['values']['ourmanagername'] = Yii::$app->user->identity->realname;
        $z_arr['values']['id'] = null;
        $z_arr['values']['dateofadmission'] = Yii::$app->formatter->asDate(time());
        $z_arr['values']['oplata_status'] = 0;
        $z_arr['values']['stage'] = 0;
        $z_arr['values']['deadline'] = null;
        $z_arr['values']['is_express'] = 0;
        $z_arr['values']['exec_speed'] = 0;
        $z_arr['values']['exec_speed_summ'] = null;
        $z_arr['values']['exec_speed_payment'] = null;
        $z_arr['values']['wages'] = Yii::$app->user->identity->wages;
        $z_arr['values']['percent1'] = Yii::$app->user->identity->percent1;
        $z_arr['values']['percent2'] = Yii::$app->user->identity->percent2;
        $z_arr['values']['percent3'] = Yii::$app->user->identity->percent3;

        /*
         * По просьбе Ильи 01.05.2020:
         * При копирование сбрасываются параметры
         * счёта для оплаты
         *
         */
        $z_arr['values']['account_number'] = NULL;
        $z_arr['values']['method_of_payment'] = 0;
        $z_arr['values']['invoice_from_this_company'] = 0;
        $z_arr['values']['zakUrFace'] = 0;
        /* Конец изменения */

        if (is_array($z_arr['values']['materials'])) {
            $z_arr['matOnFirm'] = [];
            for ($i = 0; $i < count($z_arr['values']['materials']); $i++) {
                if ($tmp = MaterialsOnFirms::find()->where([
                            'firm_id' => $z_arr['values']['materials'][$i]['firm_id'],
                            'm_type'  => $z_arr['values']['materials'][$i]['type_id'],
                            'm_id'    => $z_arr['values']['materials'][$i]['mat_id'],
                        ])->one()) {
                    if ((double) $z_arr['values']['materials'][$i]['coast'] < (double) $tmp->coast) {
                        $z_arr['values']['materials'][$i]['coast'] = $tmp->coast;
                        $z_arr['matOnFirm'][] = ['Исправлен', $z_arr['values']['materials'][$i]];
                    }
                }
            }
        }
        if ($re_print)
            $this->copyProceeedRePrint($z_arr, $re_print);
    }

    protected function toEdit(&$zak) {
        $toCopy = (bool) Yii::$app->request->post('isToCopy', false);
        $re_print = (int) Yii::$app->request->post('copyAsReprint', 0);
        $zak->dateofadmission = $zak->dateofadmission ? Yii::$app->formatter->asDate($zak->dateofadmission) : $zak->dateofadmission;
        $zak->deadline = $zak->deadline ? Yii::$app->formatter->asDate($zak->deadline) : $zak->deadline;
        $zak->date_of_receipt = $zak->date_of_receipt ? Yii::$app->formatter->asDate($zak->date_of_receipt) : $zak->date_of_receipt;
        $zak->date_of_receipt1 = $zak->date_of_receipt1 ? Yii::$app->formatter->asDate($zak->date_of_receipt1) : $zak->date_of_receipt1;
        $zak->tmpName = MyHelplers::generateRandName();
        $zak->total_coast = round($zak->total_coast);
        $zak->spending = round($zak->spending);
        $zak->spending2 = round($zak->spending2);
        $rVal = [
            'status'           => 'ok',
            'formName'         => $zak->formName(),
            'attrList'         => $zak->attributes(),
            'attributeLabels'  => $zak->attributeLabels(),
            'isOpl'            => $zak->total_coast != 0 && $zak->total_coast - ZakazOplata::find()->where(['zakaz_id' => $zak->id])->sum('summ') == 0 && !$zak->re_print,
            'productCategory'  => $zak->productCategory,
            'productCategory2' => $zak->productCategory2,
            'values'           => ArrayHelper::merge(
                    $zak->toArray($zak->attributes()
                            , ['profit', 'ourmanagername', 'podryad', 'materials', 'tmpName', 'invoice_from_this_company'])
                    , [
                'zak_idText'                => $zak->zak_idText,
                'manager_id_text'           => $zak->manager_id_text,
                'manager_phone_text'        => $zak->manager_phone_text,
                'manager_email_text'        => $zak->manager_email_text,
                'invoice_from_this_company' => (int) $zak->invoice_from_this_company,
                're_print'                  => (int) $zak->re_print,
                'wages'                     => $zak->wages,
                'percent1'                  => $zak->percent1,
                'percent2'                  => $zak->percent2,
                'percent3'                  => $zak->percent3,
            ]),
            'executersList'    => Pod::find()->where(['status' => true])->orderBy('case WHEN `firm_id`>3 THEN `mainName` ELSE null end ASC')->asArray()->all(),
            'allFileList'      => MyHelplers::zipListById($zak->id ? $zak->id : 0),
            'zakId'            => (string) $zak->id,
        ];
        if ($toCopy) {
            if ($rVal['values']['re_print'] != 0) {
                return [
                    'status'     => 'error',
                    'headerText' => 'Ошибка',
                    'errorText'  => 'Нельзя копировать перепечатку'
                ];
            } else
                $this->copyProceeed($rVal, $re_print);
        }
        return $rVal;
    }

    protected function moveFileToArchive(&$zak, $tmpPath) {
        $error = [];
        if (is_dir($tmpPath . '/files')) {
            $files = FileHelper::findFiles($tmpPath . '/files');
            if (count($files)) {
                $ZipfName = MyHelplers::zipPathToStore($zak->id);
                $zip = new \ZipArchive();
                if (file_exists($ZipfName))
                    $oRes = $zip->open($ZipfName);
                else
                    $oRes = $zip->open($ZipfName, \ZipArchive::CREATE);
                if ($oRes === true) {
                    $dirDest = 'Z' . MyHelplers::formatName((string) $zak->id, '0', 8);

//                    $zip->addEmptyDir($dirDest);
                    foreach ($files as $file) {
                        Yii::trace(basename($file), 'zip - обр');
                        if (file_exists($file)) {
                            Yii::trace(basename($file), 'zip найден');
                            if ($zip->addFile($file, $dirDest . '/' . basename($file)) !== true) {
                                $error[] = 'ZIP неудалось добавить файл "' . $file . '"';
                            } else {
                                Yii::trace(basename($file) . '->' . $dirDest . '/' . basename($file), 'zip сохранен');
                            }
                        }
                    }
                    $zip->close();
                } else {
                    $zip->close();
                    $error[] = 'Неудалось открыть zip файл "' . $ZipfName . '"';
                }
            }
        }
        return $error;
    }

    protected function removePreparedFiles(&$zak) {
        $tmpName = $zak->tmpName;
        $error = [];
        $tempSubF = $this->pathToRemoveFileStored($tmpName);
        if (file_exists($tempSubF)) {
            $list = $this->toRemoveFileStored($tmpName);
            if (count($list)) {
                $ZipfName = MyHelplers::zipPathToStore($zak->id);
                $zip = new \ZipArchive();
                if (file_exists($ZipfName)) {
                    if ($oRes = $zip->open($ZipfName) === true) {
                        $dirSrch = 'Z' . MyHelplers::formatName((string) $zak->id, '0', 8);
                        if (isset($list[$zak->id])) {
                            foreach ($list[$zak->id] as $index) {
                                $zip->deleteIndex($index);
                            }
                        }
                        if (!$zip->numFiles) {
                            $zip->close();
                            unlink($ZipfName);
                        } else
                            $zip->close();
                    } else
                        $error[] = 'Неудалось открыть zip файл "' . $ZipfName . '"';
                } else
                    $error[] = 'Архив "' . $ZipfName . '" не найден';
            }
        }
        return $error;
    }

    private function _fileCopyAction(&$list, $perfix, $tmpPath, &$zipSrch, &$zipDest, $dirDest, &$errors) {
        foreach ($list[$perfix] as $key => $fName) {
            $tmpFName = $tmpPath . '/zipoperation' . $key . '.tmp';
            if (file_put_contents($tmpFName, $zipSrch->getFromIndex((int) $key)) !== false) {
                if ($zipDest->addFile($tmpFName, $dirDest . '/' . $perfix . '_' . basename($fName)) !== true) {
                    $errors[] = 'ZIP неудалось добавить файл "' . $fName . '"';
                }
            } else {
                $errors[] = 'Неудалось извлечь файл "' . $fName . '"';
            }
        }
    }

    private function fileCopyProceed(int $srchId, int $destId, $tmpPath) {
        $errors = [];
        $closeSrch = false;
        $list = MyHelplers::zipListById($srchId);
        $ZipfNameDest = MyHelplers::zipPathToStore($destId);
        $zipDest = new \ZipArchive();
        if (file_exists($ZipfNameDest))
            $oRes = $zipDest->open($ZipfNameDest);
        else
            $oRes = $zipDest->open($ZipfNameDest, \ZipArchive::CREATE);
        $ZipfNameSrch = MyHelplers::zipPathToStore($srchId);
        if ($ZipfNameDest === $ZipfNameSrch) {
            $zipSrch = $zipDest;
            $oRes2 = true;
        } else {
            $closeSrch = true;
            $zipSrch = new \ZipArchive();
            if (file_exists($ZipfNameSrch))
                $oRes2 = $zipSrch->open($ZipfNameSrch);
            else
                $oRes2 = $zipSrch->open($ZipfNameSrch, \ZipArchive::CREATE);
        }
        if ($oRes === true && $oRes2 === true) {
            $dirDest = 'Z' . MyHelplers::formatName((string) $destId, '0', 8);
            $this->_fileCopyAction($list, 'main', $tmpPath, $zipSrch, $zipDest, $dirDest, $errors);
            $this->_fileCopyAction($list, 'des', $tmpPath, $zipSrch, $zipDest, $dirDest, $errors);
            $zipDest->close();
            if ($closeSrch)
                $zipSrch->close();
        } else {
            if ($oRes !== true) {
                $errors[] = 'Неудалось открыть zip файл "' . $ZipfNameDest . '"';
            }
            if ($oRes2 !== true) {
                $errors[] = 'Неудалось открыть zip файл "' . $ZipfNameSrch . '"';
            }
        }
        return $errors;
    }

    protected function saveProceed(&$zak) {
        $isNew = $zak->isNewRecord;
        if ($zak->load(Yii::$app->request->post(), 'Zakaz')) {
            if ($zak->save()) {
                Yii::$app->user->identity->lastZakaz = $zak->id;
                $rVal = ['status' => 'saved'];
                $zipError = [];
                $tmpPath = $this->createTempPathAndCheckFolderExist($zak->tmpName);
                if ($zak->tmpName && file_exists($tmpPath)) {
                    $zipError = $this->moveFileToArchive($zak, $tmpPath);
                    $removeError = $this->removePreparedFiles($zak);
                    if ($srchId = (int) Yii::$app->request->post('copyFileFrom', 0)) {
                        $copyErrors = $this->fileCopyProceed($srchId, $zak->id, $tmpPath);
                    } else {
                        $copyErrors = [];
                    }
                    if (!count($zipError) && !count($removeError) && !count($copyErrors))
                        FileHelper::removeDirectory($tmpPath);
                    else {
                        $rVal['warn'] = [];
                        foreach ($zipError as $val)
                            $rVal['warn'][] = $val;
                        foreach ($removeError as $val)
                            $rVal['warn'][] = $val;
                        foreach ($copyErrors as $val)
                            $rVal['warn'][] = $val;
                    }
                }
                if ($tmpMenu = $this->controller->showMessage(true)) {
                    $rVal['label'] = $tmpMenu['label'];
                    $rVal['menu'] = \yii\bootstrap\Dropdown::widget(['items' => $tmpMenu['items'], 'options' => ['class' => 'nav-mess-main']]);
                    $rVal['lastZakaz'] = $zak->id;
                    $rVal['isNew'] = $isNew;
                }
                return $rVal;
            } else {
                return [
                    'status'    => 'error',
                    'errorText' => 'Ошибка сохранения! Заказ №' . $this->postID,
                    'errors'    => $zak->errors,
                ];
            }
        } else {
            if ($zak->isNewRecord) {
                Yii::$app->user->identity->lastZakaz = null;
            } else {
                Yii::$app->user->identity->lastZakaz = $zak->id;
            }
            return $this->toEdit($zak);
        }
    }

}
