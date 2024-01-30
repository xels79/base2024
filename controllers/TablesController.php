<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of BanksController
 *
 * @author Александр
 */
use Yii;
use app\controllers\ControllerMain;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use \app\models\tables\Productions;
use app\models\tables\Tablestypes;
use app\components\MyHelplers;
use app\models\Options;
use yii\helpers\Json;

class TablesController extends ControllerMain {

    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'                => ['post'],
                    'validate'            => ['post'],
                    'addedit'             => ['post'],
                    'remove'              => ['post'],
                    'gettecnicalsoptions' => ['post']
                ],
            ],
        ];
    }

    protected function getCacheOptionKey($name = null) {
        return MyHelplers::hashString(Yii::$app->user->identity->id . 'technicalsOpt' . Yii::$app->id);
    }

    private function optionsDefault($matStructure = null) {
        return [
            'matsturct'           => $matStructure,
            'values'              => [],
            'availabel'           => $matStructure ? ArrayHelper::index($matStructure, 'translitName') : [
            ],
            'availabelMaterOther' => [
                'material_count' => 'Количество',
                'uf_lak'         => 'Уф-лак'
            ],
            'availabelZakaz'      => [
                'format_printing_block' => 'Формат печатного блока',
                'num_of_printing_block' => 'Кол-во печатных блоков',
            ]
        ];
    }

    protected function getOptionsModel($name = null) {
        if (!($model = Options::find()->where([
                    'optionid' => 'tecnikal_1'
                ])->one())) {
            $model = new Options();
            $model->userid = (int) Yii::$app->user->identity->id;
            $model->optionid = 'tecnikal_1';
            if ($tmp = Tablestypes::find()->asArray()->all()) {
                $options = $this->optionsDefault($tmp);
            } else {
                $options = $this->optionsDefault();
            }
            $model->options = $options;
        }
        return $model;
    }

    public function actionGettecnicalsoptions() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($options = Yii::$app->request->post('options')) {
            if (!$model = Options::find()->where([
                        'optionid' => 'tecnikal_1'
                    ])->one()) {
                $model = new Options();
                $model->userid = (int) Yii::$app->user->identity->id;
                $model->optionid = 'tecnikal_1';
                if ($tmp = Tablestypes::find()->asArray()->all()) {
                    $options = $this->optionsDefault($tmp);
                } else {
                    $options = $this->optionsDefault();
                }
            }
            Yii::trace('opt', \yii\helpers\VarDumper::dumpAsString($model->options, 10, true));
            $model->options = $options;
            Yii::trace('opt', \yii\helpers\VarDumper::dumpAsString($model->options, 10, true));
            if ($model->save()) {
                $options = $this->getOptions(false);
                return ['status' => 'ok', 'options' => $options, 'post' => Yii::$app->request->post()];
            } else {
                return ['status' => 'error', 'errors' => $model->errors];
            }
        } else
            return $this->getOptions();
    }

    public function actionValidate($classN = null, $tName = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int) Yii::$app->request->post('id', 0);
        if ($classN === null)
            $classN = Yii::$app->request->post('formName');
        if (!$classN) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        $fullName = 'app\\models\\tables\\' . $classN;
        if (is_string($tName))
            $model = $fullName::createObject($tName);
        else
            $model = new $fullName();
        if ($id)
            $model = $model->findOne($id);
        if ($model) {
            $model->load(Yii::$app->request->post(), $classN);
            if ($model->validate(array_keys(Yii::$app->request->post($classN, [
                    ])))) {
                return ['status' => 'ok', 'post' => $_POST];
            } else {
                return ['status' => 'error', 'errors' => $model->errors];
            }
        } else {
            return ['status' => 'error', 'errors' => [], 'errorText' => "$classN: запись №$id не найден"];
        }
    }

    public function actionRemove($classN = null, $tName = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($classN === null)
            $classN = Yii::$app->request->post('formName');
        if (!$classN) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        $fullName = 'app\\models\\tables\\' . $classN;
        $id = (int) Yii::$app->request->post('id', null);
        if ($id === null) {
            return ['status'    => 'error', 'errorText' => 'Не передан первичный ключ (id)',
                'post'      => $_POST];
        }
        if (is_string($tName))
            $query = $fullName::createObject($tName);
        else
            $query = new $fullName();
        if ($model = $query->find()->where(['id' => $id])->one()) {
            $model->delete();
            return ['status' => 'ok'];
        } else {
            return ['status'    => 'error', 'errorText' => 'Запись ' . $id . ' не найдена',
                'id'        => $id];
        }
    }

    public function actionAddedit($classN = null, $tName = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($classN === null)
            $classN = Yii::$app->request->post('formName');
        if (!$classN) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        $fullName = 'app\\models\\tables\\' . $classN;
//        return ['status'=>'error','errorText'=>'test',$classN,$fullName,$tName,is_string($tName),$_POST];
        if (is_string($tName))
            $model = $fullName::createObject($tName);
        else
            $model = new $fullName();

        if ($id = Yii::$app->request->post('id', false)) {
            if (!$model = $model->findOne((int) $id)) {
                return ['status'    => 'error', 'errorText' => 'Запись ' . $id . ' не найдена',
                    'id'        => $id];
            }
        }

        if ($model->load(Yii::$app->request->post(), !$tName ? $classN : $tName )) {
            if ($model->save()) {
                $tmp = [
                    'status' => 'ok',
                    'item'   => [
                        'id'   => $model->id,
                        'name' => $model->name,
                    //'category' => isset( $model->catText ) ? $model->catText : null
                    ]
                ];
                if ($classN !== 'Worktypes' && $classN !== 'Postprint') {
                    $tmp['item']['category'] = isset($model->catText) ? $model->catText : null;
                }
                if ($classN === 'Productions' || $classN === 'WorkOrproductType')
                    $tmp['item']['category2'] = $model->cat2Text;
                return $tmp;
            } else {
                return ['status' => 'error', 'errors' => $model->errors];
            }
        } else {
            return [
                'status'                   => 'ok',
                'formName'                 => !$tName ? $classN : $tName,
                'prim'                     => $model->id,
                !$tName ? $classN : $tName => $model->toArray(((!$tName ? $classN : $tName) === 'Worktypes' || (!$tName ? $classN : $tName) === 'Postprint') ? [
                    'id', 'name'] : ['id', 'name', 'category', 'category2']),
            ];
        }
    }

    public function actionList($classN = null, $tName = null, $where = null, $pageSize = 0) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($classN === null)
            $classN = Yii::$app->request->post('formName');
        if (!$classN) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        $fullName = 'app\\models\\tables\\' . $classN;
        $page = Yii::$app->request->post('page', 0);
        (int) $pageSize = $pageSize ? $pageSize : (int) Yii::$app->request->post('pageSize', 1800);
        $offset = $page * $pageSize;
        if (is_string($tName))
            $query = $fullName::createObject($tName);
        else
            $query = $fullName::find();
        if ($classN === 'Tablestypes' || $classN === 'Productions' || $classN === 'Worktypes' || $classN === 'Postprint')
            $query->orderBy('name');
        if (is_array($where))
            $query->where($where);
        $query->limit($pageSize)->offset($offset);
        $result = [];
        foreach ($query->each() as $row) {
            $tmp = [
                'id'       => $row->id,
                'name'     => $row->name,
                'category' => isset($row->catText) ? $row->catText : null
            ];
            if ($classN === 'Productions' || $classN === 'WorkOrproductType') {
                $tmp['category2'] = isset($row->cat2Text) ? $row->cat2Text : null;
            } else {
                if ($classN === 'Worktypes' || $classN === 'Postprint') {
                    unset($tmp['category']);
                }
            }
            $result[] = $tmp;
        }
        return ['status'   => 'ok', 'result'   => $result, 'pageSize' => $pageSize,
            'count'    => $fullName::find()->count(),
            'post'     => $_POST];
    }

}
