<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of ajaxController
 *
 * @author Александр
 */
use Yii;
use \app\controllers\ControllerTait;

class AjaxController extends ControllerMain {

    use ControllerTait;

    public function beforeAction($action) {
        $rVal = parent::beforeAction($action);
//        if ($action->id=='index') Url::remember();
        if (!\yii::$app->request->isPost)
            \yii\helpers\Url::remember();
        $this->layout = 'main_2.php';
        return $rVal;
    }

    protected function _removeSF($req, $id, $classN = 'RekvizitOur') {
        if (!$id) {
            return ['status' => 'error', 'errorText' => 'id записи не передан'];
        } else {
            $fullName = 'app\\models\\admin\\' . $classN;
            $rekvizit = $fullName::findOne((int) $id);

            if ($rekvizit) {
                $rekvizit->delete();
                Yii::$app->cache->flush();
                return ['status' => 'ok', 'html' => 'Запись удалена!'];
            } else {
                return ['status' => 'error', 'errorText' => "Запись №$id не найдена", 'post' => $_POST];
            }
        }
    }

    protected function _viewSF($req, $id, $classN = 'RekvizitOur', $formFile = '_viewourrekvizit') {
        if (!$id) {
            return ['status' => 'error', 'errorText' => 'id записи не передан'];
        } else {
            $fullName = 'app\\models\\admin\\' . $classN; //\Yii::getAlias('@app/models/admin/'.$classN);
            $model = $fullName::findOne((int) $id);
            if ($model) {
                return ['status' => 'form', 'html' => $this->renderPartial($formFile, ['model' => $model])];
            } else {
                return ['status' => 'error', 'errorText' => "Запись №$id не найдена", 'post' => $_POST, 'fullName' => $fullName];
            }
        }
    }

    protected function _changeSF($req, $id, $classN = 'RekvizitOur', $formFile = '_addourrekvizit') {
        if (!$id) {
            return ['status' => 'error', 'errorText' => 'id записи не передан'];
        } else {
            $fullName = 'app\\models\\admin\\' . $classN; //\Yii::getAlias('@app/models/admin/'.$classN);
            $model = $fullName::findOne((int) $id);
            if ($model) {
                if ($model->load(Yii::$app->request->post(), $classN)) {
                    if ($model->save() && (($classN !== 'OurFirm' && $classN !== 'RekvizitOur' && $classN !== 'ManagersOur') || $model->saveFiles())) {
                        return ['status' => 'saved', 'modelName' => $model->formName(), 'errors' => $model->errors, 'files' => $_FILES, 'post' => $_POST];
                    } else {
                        return [
                            'status'    => 'error',
                            'modelName' => $model->formName(),
                            'errors'    => $model->errors,
                            'errorText' => 'Ошибка сохранения',
                        ];
                    }
                } else {
                    return ['status' => 'form', 'html' => $this->renderPartial($formFile, ['model' => $model, 'otherRequestParam' => $req, 'classN' => $classN]), 'modelName' => $model->formName(), 'errors' => $model->errors, 'files' => $_FILES, 'post' => $_POST];
                }
            } else
                return ['status' => 'error', 'errorText' => "Запись №$id не найдена"];
        }
    }

    protected function _addSF($req, $classN = 'RekvizitOur', $formFile = '_addourrekvizit') {
        $fullName = 'app\\models\\admin\\' . $classN; //\Yii::getAlias('@app/models/admin/'.$classN);
        $model = new $fullName();
        if ($model->load(Yii::$app->request->post(), $classN)) {
            if ($model->validate()) {
                $modelTmp = new $fullName();
                if ($model->canGetProperty('mainName')) {
                    if (!$tmp = $modelTmp->find()->where(['mainName' => $model->mainName])->andWhere(['mainForm' => $model->mainForm])->one()) {
                        if ($model->save() && (($classN !== 'RekvizitOur' && $classN !== 'ManagersOur') || $model->saveFiles())) {
                            Yii::$app->cache->flush();
                            return ['status' => 'saved', 'firm_id' => $model->firm_id];
                        } else {
                            return [
                                'status'    => 'error',
                                'modelName' => $model->formName(),
                                'errors'    => $model->errors,
                                'errorText' => 'Ошибка сохранения',
                            ];
                        }
                    } else {
                        $model->addError('mainName', 'и именем "' . $model->mainName . '" уже существует.');
                        $model->addError('mainForm', 'Фирма с Формой "' . $model->mainFormText . '"');
                        return [
                            'status'    => 'error',
                            'modelName' => $model->formName(),
                            'errors'    => $model->errors,
                            'tmp'       => $tmp,
                            'errorText' => 'Ошибка сохранения<br>Фирма с именем "' . $model->mainName . '" и<br>Формой "' . $model->mainFormText . '" уже существует.',
                        ];
                    }
                } else {
                    if ($model->save() && (($classN !== 'RekvizitOur' && $classN !== 'ManagersOur') || $model->saveFiles())) {
                        return ['status' => 'saved', 'firm_id' => $model->firm_id];
                    } else {
                        return [
                            'status'    => 'error',
                            'modelName' => $model->formName(),
                            'errors'    => $model->errors,
                            'errorText' => 'Ошибка сохранения',
                        ];
                    }
                }
            } else {
                return ['status' => 'error', 'errors' => $model->errors, 'errorText' => 'Ошибка валидации', 'modelName' => $model->formName(), 'files' => $_FILES, 'post' => $_POST];
            }
        }
        return ['status' => 'form', 'html' => $this->renderPartial($formFile, ['model' => $model, 'otherRequestParam' => $req, 'classN' => $classN])];
    }

    protected function _validateSF($req, $id = 0, $classN = 'RekvizitOur', $formFile = '_addourrekvizit') {
        $fullName = 'app\\models\\admin\\' . $classN; //\Yii::getAlias('@app/models/admin/'.$classN);
        if ($id !== 0) {
            $model = $fullName::findOne((int) $id);
        } else {
            $model = new $fullName();
        }
        if ($model->load(Yii::$app->request->post(), $classN)) {
            $keys = array_keys(Yii::$app->request->post($classN, []));
            if ($model->validate($keys)) {
                return ['status' => 'ok'];
            } else {
                return ['status' => 'error', 'errors' => $model->errors, 'errorText' => 'Ошибка валидации', 'modelName' => $model->formName()];
            }
        }
        return ['status' => 'form', 'html' => $this->renderPartial($formFile, ['model' => $model, 'otherRequestParam' => $req])];
    }

}
