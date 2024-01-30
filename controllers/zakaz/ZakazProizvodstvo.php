<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use \app\models\zakaz\Zakaz;
use app\models\admin\ManagersOur;
use app\models\tables\Pechiatnik;

/**
 * Description of ZakazProizvodstvo
 *
 * @author Александр
 */
class ZakazProizvodstvo extends ZakazDisainer {

    public static $proizvodstvoCanChangeTo = [3, 6, 7, 8, 9]; //Может изменить на
    public static $proizvodstvoCanChangeSee = [1, 4, 5, 6, 9]; //Видит
    public static $availableColumnsProizvodstvo = [
        'dateofadmissionText',
        'deadlineText',
        'zak_idText',
        'ourmanagername',
        'name',
        'production_idText',
        'number_of_copiesText',
        'number_of_copies1Text',
        'stageText',
        'pechiatnikTxt',
    ];

    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
                    'verbs' => [
                        'class'   => VerbFilter::className(),
                        'actions' => [
                            'proizvodstvoindex'               => ['get'],
                            'setsizesmproizvodstvo'           => ['post'],
                            'getavailablecolumnsproizvodstvo' => ['post'],
                            'setcolumnsproizvodstvo'          => ['post'],
                            'listproizvodstvo'                => ['post'],
                            'stageproizvodstvo'               => ['post'],
                            'addpechatnik'                    => ['post'],
                            'removepechatnik'                 => ['post'],
                            'totomorowpechatnik'              => ['post'],
                            'toreadypechatnik'                => ['post'],
                        ]
                    ]
        ]);
    }

    public function actionProizvodstvoindex() {
        $this->layout = 'main_2.php';
        return $this->render('proizvodstvoindex', ['pechatnikTable' => $this->getPechatnikTable()]);
    }

    public function actionGetavailablecolumnsproizvodstvo() {
        return $this->availabelColumns('materialListProizvodstvo', self::$availableColumnsProizvodstvo);
    }

    public function actionSetcolumnsproizvodstvo() {
        return $this->setcolumns('materialListProizvodstvo');
    }

    public function actionSetsizesmproizvodstvo() {
        return $this->setsizesm('materialListProizvodstvo');
    }

    public function actionStageproizvodstvo() {
        $id = (int) Yii::$app->request->post('id', 0);
        $stage = (int) Yii::$app->request->post('stage', -1);
        if ($id && $stage > -1 && in_array($stage, self::$proizvodstvoCanChangeTo)) {
            if ($model = Zakaz::findOne($id)) {
                $model->stage = $stage;
                if ($model->update(false, ['stage'])) {
                    if ($this->checkPechatnikOnStageChange($id, $stage)) {
                        return ['status' => 'ok', 'pechatnikTable' => $this->getPechatnikTable()];
                    } else {
                        return ['status' => 'ok'];
                    }
                } else {
                    return [
                        'status'    => 'error',
                        'errorText' => "Заказ $id не удалось сохранить",
                        'errors'    => $model->errors
                    ];
                }
            } else
                return ['status' => 'error', 'errorText' => "Заказ $id не найден"];
        } else {
            return ['status'    => 'error', 'errorText' => 'Неверное значение ID или STAGE',
//                [
//                    'dop' => [
//                        'post'                     => Yii::$app->request->post(),
//                        '$proizvodstvoCanChangeTo' => self::$proizvodstvoCanChangeTo
//                    ]
            ];
        }
    }

    private function getPechatnikDay($sign, $dt, $isReady = false) {
        $rVal = Pechiatnik::find()
                ->leftJoin('zakaz', 'zakaz.id=pechiatnik.z_id')
                ->leftJoin('managerOur', 'managerOur.managerOur_id=pechiatnik.m_id')
                ->leftJoin('production', 'production.id=zakaz.production_id')
                ->where([$sign, 'pechiatnik.z_date', $dt])
                ->select([
            'pechiatnik.id as pechiatnik_id',
            'pechiatnik.z_id as z_id',
            'pechiatnik.z_date as z_date',
            'pechiatnik.z_time as z_time',
            'pechiatnik.ready as ready',
            'managerOur.name as name',
            'production.name as production_text',
            'production.category as category',
            'zakaz.name as product_name_text',
            'zakaz.number_of_copies as number_of_copies',
            'zakaz.number_of_copies1 as number_of_copies1',
            'zakaz.colors as colors',
            'zakaz.post_print_uf_lak as uf_lak',
            'zakaz.post_print_thermal_lift as thermal_lift',
            'zakaz.num_of_printing_block as num_of_printing_block',
            'zakaz.num_of_printing_block1 as num_of_printing_block1'
        ]);
        if ($isReady)
            $rVal->andWhere(['not', ['pechiatnik.ready' => null]]);
        else
            $rVal->andWhere(['pechiatnik.ready' => null]);
        return ArrayHelper::index($rVal->asArray()->all(), 'z_id', 'name');
    }

    private function getPechatnikTable() {
        $dt = \Yii::$app->formatter->asDate(new \DateTime, 'php:Y-m-d');
        return [
            'date'    => $dt,
            'tomorow' => $this->getPechatnikDay('>', $dt),
            'today'   => $this->getPechatnikDay('<=', $dt),
            'ready'   => ArrayHelper::merge($this->getPechatnikDay('<=', $dt, true), $this->getPechatnikDay('>', $dt, true))
        ];
    }

    public function actionTotomorowpechatnik() {
        if ($z_id = (int) Yii::$app->request->post('z_id', 0)) {
            if ($model = Pechiatnik::find()->where(['z_id' => $z_id])->one()) {
                if (!$newD = Yii::$app->request->post('date')) {
                    $to = new \DateTime('now');
                    $to->add(new \DateInterval('P1D'));
                    $newD = $to->format('Y-m-d');
                } else {
                    $newD = Yii::$app->formatter->asDate($newD, 'php:Y-m-d');
                }
                $model->z_date = $newD;
                if ($model->update()) {
                    return ['status' => 'ok', 'date' => $newD];
                } else {
                    return ['status'    => 'error', 'errorText' => "Ошибка сохранения заказа №$z_id у печатников",
                        'errors'    => $model->errors];
                }
            } else {
                return ['status' => 'error', 'errorText' => "Заказ №$z_id не найден у печатников"];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передан id заказ'];
        }
    }

    public function actionToreadypechatnik() {
        if ($z_id = (int) Yii::$app->request->post('z_id', 0)) {
            if ($model = Pechiatnik::find()->where(['z_id' => $z_id])->one()) {
                $to = new \DateTime('now');
                $model->ready = $to->format('Y-m-d');
                if ($model->update()) {
                    return ['status' => 'ok', 'date' => $to->format('Y-m-d')];
                } else {
                    return ['status'    => 'error', 'errorText' => "Ошибка сохранения заказа №$z_id у печатников",
                        'errors'    => $model->errors];
                }
            } else {
                return ['status' => 'error', 'errorText' => "Заказ №$z_id не найден у печатников"];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передан id заказ'];
        }
    }

    public function actionRemovepechatnik() {
        if ($z_id = (int) Yii::$app->request->post('z_id', 0)) {
            if ($model = Pechiatnik::find()->where(['z_id' => $z_id])->one()) {
                if ($model->delete() !== false) {
                    return ['status' => 'ok', 'messagee' => 'Заказ удалён из печати'];
                } else {
                    return ['status' => 'error', 'errorText' => "Ошибка удаления заказа №$z_id у печатников"];
                }
            } else {
                return ['status' => 'error', 'errorText' => "Заказ №$z_id не найден у печатников"];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передан id заказ'];
        }
    }

    public function actionAddpechatnik() {
        if ($z_id = (int) Yii::$app->request->post('z_id', 0)) {
            if (!$p = Pechiatnik::find()->where(['z_id' => $z_id])->one()) {
                $p = new Pechiatnik();
                $p->z_id = $z_id;
                $p->m_id = (int) Yii::$app->request->post('p_id', 0);
                $p->z_date = Yii::$app->formatter->asDate(Yii::$app->request->post('z_date', ''), 'php:Y-m-d');
                $p->z_time = (string) Yii::$app->request->post('p_time', '');
                if ($p->save()) {
                    return[
                        'status' => 'ok',
                        'p_data' => $this->getPechatnikTable(),
                    ];
                } else {
                    return[
                        'status'    => 'error',
                        'errorText' => 'Ошибка при сохранение модели',
                        'errors'    => $p->errors
                    ];
                }
            } else {
                if ($mO = ManagersOur::findOne($p->m_id)) {
                    $nm = $mO->name;
                } else {
                    $nm = 'не найден';
                }
                return ['status' => 'error', 'errorText' => 'Заказ №' . $z_id . ' уже добавлен<br>к печатнику: ' . $nm];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передан id заказ'];
        }
    }

    public function actionListproizvodstvo() {
        if (($page = (int) Yii::$app->request->post('page', 0)) < 0)
            $page = 0;
        $rVal = [
            'list'       => [],
            'hidden'     => [],
            'attention'  => [],
            'colOptions' => $this->getOptions(true, 'materialListProizvodstvo'),
        ];
        $rVal['pageSize'] = (int) (isset($rVal['colOptions']['pageSize']) ? $rVal['colOptions']['pageSize'] : 10);
        $colsN = $this->getCollName($rVal['colOptions']);
        //where(['=','stage',4])->orWhere(['=','stage',5])->orWhere(['=','stage',6]);
        $queryPrepary = Zakaz::find()->where(['in', 'stage', self::$proizvodstvoCanChangeSee]);
        $this->filterApplay($queryPrepary, false, true);
        $rVal['count'] = $queryPrepary->count();
        $pageCnt = (int) ceil($rVal['count'] / $rVal['colOptions']['pageSize']);
        if ($page >= $pageCnt)
            $page = $pageCnt - 1;
        $rVal['page'] = $page;
        $queryPrepary = Zakaz::find()->where(['in', 'stage', self::$proizvodstvoCanChangeSee])
                ->offset($page * $rVal['colOptions']['pageSize'])
                ->limit($rVal['colOptions']['pageSize']);
        $this->filterApplay($queryPrepary, false, true);
        $rVal['tmpSort'] = $this->orderSting;
        $queryPrepary->with('pechiatnik.managersOur');
        $queryPrepary->orderBy($this->orderSting);
//        $queryPrepary->select[]='*';
//        $queryPrepary->select[]='managerOur.name as pechiatnikText';
        $query = $queryPrepary->all();
        if ($query) {
            $diffCol = array_diff($colsN, $query[0]->attributes());
            $opt = $rVal['colOptions'];
            foreach ($query as $el) {
                $tmpRv = [];
                foreach ($colsN as $key) {
                    if ($key === 'pechiatnikTxt') {
                        if ($el['pechiatnik']['managersOur']) {
                            if ($tmpRv[$key] = $el['pechiatnik']['managersOur']['name'])
                                $tmpRv[$key] = $el['pechiatnik']['managersOur']['name'];
                            else
                                $tmpRv[$key] = 'удалён/не найден';
                        } else {
                            $tmpRv[$key] = 'Нет';
                        }
                    } else {
                        $tmpRv[$key] = $el[$key];
                    }
                }
                $rVal['list'][] = $tmpRv;
                $rVal['hidden'][$el['id']] = ['stage' => $el['stage']];
            }
        }
        $rVal['query'] = $query;
        $rVal['pCnt'] = $pageCnt;
        $rVal['colN'] = $colsN;
        $rVal['sortable'] = ['dateofadmissionText', 'deadlineText'];
        $rVal['filters'] = $this->_filters;
        $rVal['stageLevels'] = Zakaz::$_stage;
        $rVal['pechatnikTable'] = $this->getPechatnikTable();
        $rVal['pechatiks'] = ManagersOur::find()
                        ->where(['post' => 3])
                        ->select(['managerOur.managerOur_id as id', 'name'])
                        ->orderBy('name')
                        ->asArray()->all();
        return $rVal;
    }

}
