<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of MaterialtableController
 *
 * @author Александр
 */
use yii;
use app\controllers\ControllerMain;
use \app\controllers\ControllerTait;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\models\tables\MaterialsOnFirms;
use app\models\tables\MaterialsOnFirmsSearch;
use yii\helpers\Html;
use app\models\admin\Post;
use app\models\tables\Tablestypes;

class MaterialtableController extends ControllerMain {

    use ControllerTait;

    protected function getCacheOptionKey($name = null) {
        return MyHelplers::hashString(Yii::$app->user->identity->id . 'materialtablecurency' . Yii::$app->id);
    }

    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
                    'verbs' => [
                        'actions' => [
                            'index'  => ['post'],
                            'list'   => ['post'],
                            'update' => ['post']
                        ]
                    ]
        ]);
    }

    public function actionIndex() {
        return $this->renderPartial('index');
    }

    private function createFirstColumn() {
        return [
            ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['class' => 'SCol']],
            [
                'attribute'      => 'firmName',
                'label'          => 'Фирма',
                'filter'         => ArrayHelper::map(Post::find()->select(['firm_id', 'mainName'])->orderBy('mainName')->asArray()->all(), 'firm_id', 'mainName'),
                'headerOptions'  => ['class' => 'FirmCOl'],
                'contentOptions' => ['class' => 'FirmCOl'],
            ],
            [
                'attribute'      => 'matType',
                'label'          => 'Материал',
                'filter'         => ArrayHelper::map(Tablestypes::find()->select(['id', 'name'])->orderBy('name')->asArray()->all(), 'id', 'name'),
                'headerOptions'  => ['class' => 'MatCol'],
                'contentOptions' => ['class' => 'MatCol']
            ],
        ];
    }

    private function createDefaultColumn() {
        $rVal = $this->createFirstColumn();
        //$rVal[]='name:text:Сводка';
        $rVal[] = [
            'label'          => 'Сводка',
            'contentOptions' => ['class' => 'Svodka'],
            'content'        => function ($model, $key, $index, $column) {
                return Html::tag('span', $model->name);
            },
            'headerOptions'  => ['class' => 'SvodkaCol'],
            'contentOptions' => ['class' => 'Svodka']
        ];
        return $rVal;
    }

    /*
     * @property
     * @array app\models\tables\MaterialsOnFirmsSearch $searchModel В модели должна быть функция настройки колонок
     */

    private function createExtendetColumns(&$searchModel) {
        $struct = $searchModel->calculated_colum($searchModel->matType);
        $rVal = $this->createFirstColumn();
        for ($i = 0; $i < count($struct); $i++) {
            $rVal[] = [
                'label'     => $struct[$i]['label'],
                'attribute' => 'cCol_' . $i,
                'filter'    => $struct[$i]['filter'],
            ];
        }
        Yii::debug(['$struct' => $struct, '$rVal' => $rVal], 'createExtendetColumns');
        return $rVal;
    }

    public function actionUpdate() {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $newCost = Yii::$app->request->post('coast', null);
        $newOptfrom = Yii::$app->request->post('optfrom', null);
        $newOptcoast = Yii::$app->request->post('optcoast', null);
        $newRecomendetcoast = Yii::$app->request->post('recomendetcoast', null);
        if (($id = Yii::$app->request->post('id')) && ($newCost !== null || $newOptfrom !== null || $newOptcoast !== null || $newRecomendetcoast !== null)) {
            if ($model = MaterialsOnFirms::find()->where(['id' => (int) $id])->with('firm')->one()) {
                if ($newCost) {
                    $model->coast = (double) $newCost;
                }
                if (!is_null($newOptcoast)) {
                    $model->optcoast = (double) $newOptcoast;
                }
                if (!is_null($newOptfrom)) {
                    $model->optfrom = (int) $newOptfrom;
                }
                if (!is_null($newRecomendetcoast)) {
                    $model->recomendetcoast = (double) $newRecomendetcoast;
                }
                if ($model->update()) {
                    return [
                        'status'        => 'ok',
                        'coast_rub'     => $model->firm->curency_type === 'RUB' ? (double) $model->coast : round((double) $model->coast * round($this->currencies[$model->firm->curency_type], 2), 2),
                        'coast_rub_opt' => $model->firm->curency_type === 'RUB' ? (double) $model->optcoast : round((double) $model->optcoast * round($this->currencies[$model->firm->curency_type], 2), 2),
                    ];
                } else {
                    return ['status' => 'error', 'errorText' => "Ошибка сохранения заказа #$id", 'errors' => $model->errors];
                }
            } else {
                return ['status' => 'error', 'errorText' => "Заказ #$id не найден"];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передан id или coast'];
        }
    }

    public function actionList() {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $searchModel = new MaterialsOnFirmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        $this->layout = false;
        $cols = $searchModel->matType ? $this->createExtendetColumns($searchModel) : $this->createDefaultColumn();
        $cols[] = [
            'label' => 'РУБ',
            'value' => function ($model, $key, $index, $column) {
                if ($model->firm->curency_type === 'RUB') {
                    return $model->coast . ($model->optcoast ? ('/' . $model->optcoast) : '');
                } else {
                    return round($model->coast * round($this->currencies[$model->firm->curency_type], 2), 2)
                            . ($model->optcoast ? ('/' . round($model->optcoast * round($this->currencies[$model->firm->curency_type], 2), 2)) : '');
                }
            },
            'headerOptions'  => ['class' => 'CInRub'],
            'contentOptions' => ['class' => 'CInRubTD'],
        ];
        $cols[] = [
            'label'          => 'Валюта',
            'attribute'      => 'firm.curency_type',
            'headerOptions'  => ['class' => 'CType'],
            'contentOptions' => ['class' => 'CTypeTD']
        ];

        $cols[] = [
            'attribute' => 'coast',
            'label'     => 'У.Е.',
            'content'   => function ($model, $key, $index, $column) {
                return Html::input('text', null, $model->coast);
            },
            'headerOptions'  => ['class' => 'CInUE'],
            'contentOptions' => ['class' => 'CInUE'],
        ];
        $cols[] = [
            'attribute' => 'optfrom',
            'label'     => 'ОПТ от',
            'content'   => function ($model, $key, $index, $column) {
                return Html::input('text', null, $model->optfrom);
            },
            'headerOptions'  => ['class' => 'CInUE'],
            'contentOptions' => ['class' => 'CInUE'],
        ];
        $cols[] = [
            'attribute' => 'optcoast',
            'label'     => 'ОПТ У.Е.',
            'content'   => function ($model, $key, $index, $column) {
                return Html::input('text', null, $model->optcoast);
            },
            'headerOptions'  => ['class' => 'CInUE'],
            'contentOptions' => ['class' => 'CInUE'],
        ];
        $cols[] = [
            'attribute' => 'recomendetcoast',
            'label'     => 'Прайс',
            'content'   => function ($model, $key, $index, $column) {
                return Html::input('text', null, $model->recomendetcoast);
            },
            'headerOptions'  => ['class' => 'CInUE'],
            'contentOptions' => ['class' => 'CInUE'],
        ];
        return [
//            'pCount'=>$pCount,
            'content' => GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns'      => $cols
            ])
        ];
    }

}
