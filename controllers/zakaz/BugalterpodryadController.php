<?php

/*
 * Это файл создан xel_s
 * Для проекта СУБД ver2  *
 */

namespace app\controllers\zakaz;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use \app\models\zakaz\Zakaz;
use app\models\zakaz\ZakazOplata;
use app\models\zakaz\ZakazMaterials;
use app\models\zakaz\ZakazPod;
use yii\helpers\Html;

/**
 * Description of BugalterPostav
 *
 * @author xel_s
 */
//'materialListBugalterPodryad'
class BugalterpodryadController extends ZakazListBaseFunction {

    public $formClassName = 'app\models\zakaz\Zakaz';
    public $defaultAction = 'bugalterindex';
    public $columnOptionsName = 'materialListBugalterPodryad';
    public static $availableColumnsBugalter = [
        'deadlineText',
        'dateofadmissionText',
        'ourmanagername',
        'zak_idText',
        'division_of_work_text',
        'method_of_payment_text',
//        'account_number',
        'invoice_from_this_company_text',
        'total_coastText',
        'name',
        'production_idText',
        'material_total_coast',
        'calulateProfitText',
        'podryad_total_coast',
        'podryad_total_coast_list',
//        'podryad_name_list',
        'oplata_status_text',
        'oplataText',
        'material_total_coast_list',
//        'material_firms',
        'other_spends_list',
//        'other_spends_list_col',
        'other_spends_summ',
        'worktypes_id_text'
    ];

    public function behaviors()
    {
        return ArrayHelper::merge( parent::behaviors(), [
                    'verbs' => [
                        'class'   => VerbFilter::className(),
                        'actions' => [
                            'bugalterindex'               => ['get'],
                            'setsizesmbugalter'           => ['post'],
                            'getavailablecolumnsbugalter' => ['post'],
                            'setcolumnsbugalter'          => ['post'],
                            'listbugalter'                => ['post'],
                            'stagebugalter'               => ['post'],
                            'getoplatalist'               => ['post'],
                            'updateoplatalist'            => ['post'],
                            'getonerow'                   => ['post'],
                            'setmaterialstatus'           => ['post']
                        ]
                    ]
        ] );
    }

    public function actionSetmaterialstatus()
    {
        if ( $id = Yii::$app->request->post( 'id' ) ) {
            $isPodryad = Yii::$app->request->post( 'isPodryad', 'false' ) === 'true';
            $isOtherSpend = Yii::$app->request->post( 'isOtherSpend', 'false' ) === 'true';
            if ( $isOtherSpend ) {
                if ( $zakaz_id = Yii::$app->request->post( 'zakaz_id' ) ) {
                    if ( $model = Zakaz::findOne( (int) $zakaz_id ) ) {
                        $tmp = explode( '_', $id );
                        if ( count( $tmp ) > 2 ) {
                            $model[$tmp[0] . '_' . $tmp[1] . '_paid'] = Yii::$app->request->post( 'status', 'true' ) === 'true';
                            if ( $model->update( true, [$tmp[0] . '_' . $tmp[1] . '_paid'] ) ) {
                                return ['status' => 'ok'];
                            } else {
                                return ['status' => 'error', 'errorText' => "Ошибка сохранения заказ #$zakaz_id<br>" . \yii\helpers\VarDumper::dumpAsString( $model->errors, 10, true )];
                            }
                        } else {
                            return ['status' => 'error', 'errorText' => "Не верный формат имени колонки \"$id\" не найден"];
                        }
                    } else {
                        return ['status' => 'error', 'errorText' => "Заказ #$zakaz_id не найден"];
                    }
                } else {
                    return ['status' => 'error', 'errorText' => "Не передан id_zakaz"];
                }
            } elseif ( $model = ($isPodryad ? ZakazPod::findOne( (int) $id ) : ZakazMaterials::findOne( (int) $id )) ) {
                $model->paid = Yii::$app->request->post( 'status', 'true' ) === 'true';
                if ( $model->update( true, ['paid'] ) ) {
                    return ['status' => 'ok'];
                } else {
                    return ['status' => 'error', 'errorText' => "Ошибка сохранения #$id",
                        'errors' => $model->errors];
                }
            } else {
                return ['status' => 'error', 'errorText' => "Материал #$id не найден"];
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не переда id маткриала'];
        }
    }

    public function actionGetoplatalist()
    {
        if ( $id = Yii::$app->request->post( 'id', 0 ) ) {
            $rVal = [];
            $tmp = ZakazOplata::find()->where( ['zakaz_id' => $id] )->all();
            foreach ( $tmp as $model ) {
                $rVal[] = ArrayHelper::merge( $model->toArray(), ['dateText' => $model->dateText] );
            }
            return ['status' => 'ok', 'values' => $rVal];
        } else {
            return['status' => 'error', 'errorText' => 'Не передан номер заказа'];
        }
    }

    public function actionUpdateoplatalist()
    {
        if ( $id = Yii::$app->request->post( 'id', 0 ) ) {
            $rVal = ['status' => 'ok', 'errors' => ['update' => [], 'add' => []]];
            if ( $values = Yii::$app->request->post( 'update', null ) ) {
                $tmp = ZakazOplata::find()->where( ['zakaz_id' => $id, 'id' => array_keys( $values )] )->all();
                foreach ( $tmp as $model ) {
                    $model->setAttributes( $values[$model->id], false );
                    if ( $model->update() === false ) {
                        $rVal['status'] = 'error';
                        $rVal['errorText'] = 'Ошибка обновлния';
                        $rVal['errors']['update'][] = $model->getErrorSummary( true );
                    }
                }
            }
            if ( $values = Yii::$app->request->post( 'add', null ) ) {
                foreach ( $values as $val ) {
                    $val['zakaz_id'] = $id;
                    $model = new ZakazOplata();
                    $model->setAttributes( $val, false );
                    if ( $model->save() === false ) {
                        $rVal['status'] = 'error';
                        if ( $rVal['errorText'] ) {
                            $rVal['errorText'] = [$rVal['errorText'], 'Ошибка сохранние'];
                            $rVal['errors']['add'][] = $model->getErrorSummary();
                        }
                    }
                }
            }
            if ( $values = Yii::$app->request->post( 'remove', null ) ) {
                if ( is_array( $values ) ) {
                    ZakazOplata::deleteAll( ['id' => $values] );
                }
            }
            if ( $model = Zakaz::findOne( (int) $id ) ) {
                $model->oplata_status = 0;
                //$model->setAttribute('oplata_status', 0);
                $rVal['model'] = $model->toArray();
                $rVal['isModelOk'] = $model->save( true, ['oplata_status'] );
                $rVal['modelError'] = $model->errors;
            }
            return $rVal;
        } else {
            return['status' => 'error', 'errorText' => 'Не передан номер заказа'];
        }
    }

    public function actionBugalterindex()
    {
        $this->layout = 'main_2.php';
        return $this->render( 'bugalterindex' );
    }

    public function actionGetavailablecolumnsbugalter()
    {
        return $this->availabelColumns( $this->columnOptionsName, self::$availableColumnsBugalter );
    }

    public function actionSetcolumnsbugalter()
    {
        return $this->setcolumns( $this->columnOptionsName );
    }

    public function actionSetsizesmbugalter()
    {
        return $this->setsizesm( $this->columnOptionsName );
    }

    public function actionStagebugalter()
    {
        $id = (int) Yii::$app->request->post( 'id', 0 );
        $stage = (int) Yii::$app->request->post( 'stage', -1 );
        if ( $id && $stage > -1 && ($stage === 3 || $stage === 4 || $stage === 6 || $stage === 8) ) {
            if ( $model = Zakaz::findOne( $id ) ) {
                $model->stage = $stage;
                if ( $model->update( false, ['stage'] ) ) {
                    return ['status' => 'ok'];
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
            return ['status' => 'error', 'errorText' => 'Неверное значение ID или STAGE'];
        }
    }

    public function actionGetonerow()
    {
        if ( !$id = Yii::$app->request->post( 'id' ) )
                return ['status' => 'error', 'errorText' => 'Не передан id заказа'];
        $rVal = [
            'hidden'     => null,
            'attention'  => null,
            'row'        => [],
            'colOptions' => $this->getOptions( true, $this->columnOptionsName ),
        ];
        $colsN = $this->getCollName( $rVal['colOptions'] );
//        $queryPrepary=Zakaz::find()->where(['zakaz.id'=>$id]);
//        $this->filterApplay($queryPrepary,false,true);
        if ( !$model = Zakaz::findOne( $id ) )
                return ['status' => 'error', 'errorText' => "Заказ #$id - не найден"];;
        foreach ( $colsN as $key )
            $rVal['row'][$key] = $model[$key];
        $rVal['hidden'] = ['stage' => $model['stage'], 'total_coast' => $model['total_coast'],
            'oplata' => $model['oplata']];
        $rVal['hidden2'] = $this->createHidden2();
        return $rVal;
    }

    public function actionListbugalter()
    {
        if ( ($page = (int) Yii::$app->request->post( 'page', 0 )) < 0 )
                $page = 0;
        $rVal = [
            'list'       => [],
            'hidden'     => [],
            'attention'  => [],
            'colOptions' => $this->getOptions( true, $this->columnOptionsName ),
        ];
        $rVal['pageSize'] = (int) (isset( $rVal['colOptions']['pageSize'] ) ? $rVal['colOptions']['pageSize'] : 10);
        $colsN = $this->getCollName( $rVal['colOptions'] );
        $queryPrepary = Zakaz::find(); //->where(['=','stage',4])->orWhere(['=','stage',5])->orWhere(['=','stage',6]);
        $this->filterApplay( $queryPrepary, false, true );
        $rVal['count'] = $queryPrepary->count();
        $pageCnt = (int) ceil( $rVal['count'] / $rVal['colOptions']['pageSize'] );
        if (Yii::$app->request->post( 'page', false )===false) $page=$pageCnt;
        if ( $page >= $pageCnt ) $page = $pageCnt - 1;
        $rVal['page'] = $page;
        $queryPrepary = Zakaz::find()
                ->offset( $page * $rVal['colOptions']['pageSize'] )
                ->limit( $rVal['colOptions']['pageSize'] );
        $this->filterApplay( $queryPrepary, false, true );
        $rVal['tmpSort'] = $this->orderSting;
        $queryPrepary->orderBy( $this->orderSting );
        $query = $queryPrepary->all();
        $ids = ArrayHelper::getColumn( $query, 'id' );
        if ( $query ) {
            $diffCol = array_diff( $colsN, $query[0]->attributes() );
            $opt = $rVal['colOptions'];
            foreach ( $query as $el ) {
                $tmpRv = [];
                foreach ( $colsN as $key )
                    $tmpRv[$key] = $el[$key];
                $rVal['list'][] = $tmpRv;
                $rVal['hidden'][$el['id']] = ['stage' => $el['stage'], 'total_coast' => $el['total_coast'],
                    'oplata' => $el['oplata']];
            }
        }
        $rVal['pCnt'] = $pageCnt;
        $rVal['colN'] = $colsN;
        $rVal['sortable'] = ['id', 'dateofadmissionText'];
        $rVal['filters'] = $this->_filters;
        $rVal['stageLevels'] = Zakaz::$_stage;

        //array_key_exists('zakaz_materials.zakaz_id.`zakaz.id`.firm_id', Yii::$app->controller->filters)
        $rVal['hidden2'] = $this->createHidden2( $ids );
        return $rVal;
    }

    private function createHidden2( $ids = null )
    {
        $filters = $this->filters ? $this->filters : [];
//        if (!is_array($ids)){
        $queryPrepary = Zakaz::find(); //->where(['=','stage',4])->orWhere(['=','stage',5])->orWhere(['=','stage',6]);
        $this->filterApplay( $queryPrepary, false, true );
		$queryPrepary->andWhere(['not',['stage'=>'0']]);
        $ids = ArrayHelper::getColumn( $queryPrepary->select( 'zakaz.id as id' )->asArray()->all(), 'id' );
        Yii::debug( $ids, '$ids' );
//        }

        $query = \app\models\zakaz\ZakazMaterials::find()->where( ['zakaz_id' => $ids,'supplierType'=>2] );
        if ( array_key_exists( 'zakaz_materials.zakaz_id.`zakaz.id`.firm_id', $filters ) )
                $query->andWhere( ['firm_id' => $this->filters['zakaz_materials.zakaz_id.`zakaz.id`.firm_id']] );
        $query->select( 'sum(zakaz_materials.count*zakaz_materials.coast) as material_total_coast, sum(zakaz_materials.count*zakaz_materials.coast*zakaz_materials.paid) as material_total_coast_paid' )->asArray();
        $query = $query->all();
        Yii::debug( $query, 'query' );
        $material_total_coast = count( $query ) ? $query[0]['material_total_coast'] : 0;
        $material_total_coast_paid = count( $query ) ? $query[0]['material_total_coast_paid'] : 0;

        $query = ZakazPod::find()->where( ['zakaz_id' => $ids] );
        if ( array_key_exists( 'zakaz_pod.zakaz_id.`zakaz.id`.pod_id', $filters ) ) {
            $query->andWhere( ['pod_id' => $this->filters['zakaz_pod.zakaz_id.`zakaz.id`.pod_id']] );
        }
        $query = $query->select( 'sum(payment) as tCoast, sum(payment*paid) as tPaid' )->asArray()->all();
        $podryad_total_coast_list = (int) round( count( $query ) ? $query[0]['tCoast'] : 0, 0 );
        $podryad_total_coast_list_paid = (int) round( count( $query ) ? $query[0]['tPaid'] : 0, 0 );

        $query = Zakaz::find()->where( ['id' => $ids] );
        if ( array_key_exists( 'other_spends_list', $filters ) ) {
            $spendingSummSearch = '';
            $spendingSummSearch2 = '';
            foreach ( $this->filters['other_spends_list'] as $key ) {
                if ( $spendingSummSearch ) $spendingSummSearch .= '+';
                if ( $spendingSummSearch2 ) $spendingSummSearch2 .= '+';
                if ( $key === 'exec_transport' ) {
                    $spendingSummSearch .= $key . '_payment+' . $key . '2_payment';
                    $spendingSummSearch2 .= '(' . $key . '_payment*' . $key . '_paid+' . $key . '2_payment*' . $key . '2_paid)';
                } else {
                    $spendingSummSearch .= $key . '_payment';
                    $spendingSummSearch2 .= '(' . $key . '_payment*' . $key . '_paid)';
                }
            }
        } else {
            $spendingSummSearch = 'exec_speed_payment+exec_delivery_payment+exec_markup_payment+exec_bonus_payment+exec_transport_payment+exec_transport2_payment';
            $spendingSummSearch2 = '(exec_speed_payment*exec_speed_paid)+(exec_delivery_payment*exec_delivery_paid)+(exec_markup_payment*exec_markup_paid)+(exec_bonus_payment*exec_bonus_paid)+(exec_transport_payment*exec_transport_paid)+(exec_transport2_payment*exec_transport2_paid)';
        }
        $query = $query->select( "sum(total_coast) as tCoast,sum($spendingSummSearch) as sOther, sum($spendingSummSearch2) as pOther" )->asArray()->all();
        $all_zakaz_coast = (int) count( $query ) ? $query[0]['tCoast'] : 0;
        $spending = (int) count( $query ) ? $query[0]['sOther'] : 0;
        $spendingP = (int) count( $query ) ? $query[0]['pOther'] : 0;
        Yii::debug( $query, '$query' );
        return [
            'material_total_coast_list' => $material_total_coast ? ($material_total_coast_paid ? ($material_total_coast - $material_total_coast_paid ? Html::tag( 'div',
                    Html::tag( 'span', Yii::$app->formatter->asInteger( $material_total_coast ) )
                    . Html::tag( 'span', Yii::$app->formatter->asInteger( $material_total_coast_paid ) )
                    . Html::tag( 'span', Yii::$app->formatter->asInteger( $material_total_coast - $material_total_coast_paid ) )
            ) : Html::tag( 'div', '<span></span>' . Html::tag( 'span', Yii::$app->formatter->asInteger( $material_total_coast ) ) )) : Html::tag( 'div', '<span></span><span></span>' . Html::tag( 'span', Yii::$app->formatter->asInteger( $material_total_coast ) ) )) : '',
            'podryad_total_coast_list'  => $podryad_total_coast_list ? ($podryad_total_coast_list_paid ? ($podryad_total_coast_list - $podryad_total_coast_list_paid ? Html::tag( 'div',
                    Html::tag( 'span', Yii::$app->formatter->asInteger( $podryad_total_coast_list ) )
                    . Html::tag( 'span', Yii::$app->formatter->asInteger( $podryad_total_coast_list_paid ) )
                    . Html::tag( 'span', Yii::$app->formatter->asInteger( $podryad_total_coast_list - $podryad_total_coast_list_paid ) )
            ) : Html::tag( 'div', '<span></span>' . Html::tag( 'span', Yii::$app->formatter->asInteger( $podryad_total_coast_list ) ) )) : Html::tag( 'div', '<span></span><span></span>' . Html::tag( 'span', Yii::$app->formatter->asInteger( $podryad_total_coast_list ) ) )) : '',
            'total_coastText'           => Yii::$app->formatter->asInteger( $all_zakaz_coast ),
            'calulateProfitText'        => Yii::$app->formatter->asInteger( $all_zakaz_coast - $podryad_total_coast_list - $material_total_coast - $spending ),
            'other_spends_list'         => $spending ? ($spendingP ? ($spending - $spendingP ? Html::tag( 'div',
                    Html::tag( 'span', Yii::$app->formatter->asInteger( $spending ) )
                    . Html::tag( 'span', Yii::$app->formatter->asInteger( $spendingP ) )
                    . Html::tag( 'span', Yii::$app->formatter->asInteger( $spending - $spendingP ) )
            ) : Html::tag( 'div', '<span></span>' . Html::tag( 'span', Yii::$app->formatter->asInteger( $spending ) ) )) : Html::tag( 'div', '<span></span><span></span>' . Html::tag( 'span', Yii::$app->formatter->asInteger( $spending ) ) )) : '',
            'other_spends_summ'         => Yii::$app->formatter->asInteger( $spending ),
            'podryad_total_coast'       => Yii::$app->formatter->asInteger( $podryad_total_coast_list ),
            'material_total_coast'      => Yii::$app->formatter->asInteger( $material_total_coast ),
        ];
    }

}
