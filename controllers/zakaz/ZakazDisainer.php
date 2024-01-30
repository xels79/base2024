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

//use app\models\tables\Worktypes;

/**
 * Description of ZakazDirt
 *
 * @author Александр
 */
class ZakazDisainer extends ZakazListBaseFunction {

    public $defaultAction = 'disainerindex';
    public static $availableColumnsDisainer = [
        'dateofadmissionText',
        'zak_idText',
        'ourmanagername',
        'name',
        'production_idText',
        'number_of_copiesText',
        'number_of_copies1Text',
        'stageText'
    ];

    public function behaviors()
    {
        return ArrayHelper::merge( parent::behaviors(), [
                    'verbs' => [
                        'class'   => VerbFilter::className(),
                        'actions' => [
                            'disainerindex'               => ['get'],
                            'setsizesmdisainer'           => ['post'],
                            'getavailablecolumnsdisainer' => ['post'],
                            'setcolumnsdisainer'          => ['post'],
                            'listdisainer'                => ['post'],
                            'stagedisainer'               => ['post']
                        ]
                    ]
        ] );
    }

    public function actionDisainerindex()
    {
        $this->layout = 'main_2.php';
        return $this->render( 'disainerindex' );
    }

    public function actionGetavailablecolumnsdisainer()
    {
        return $this->availabelColumns( 'materialListDisainer', self::$availableColumnsDisainer );
    }

    public function actionSetcolumnsdisainer()
    {
        return $this->setcolumns( 'materialListDisainer' );
    }

    public function actionSetsizesmdisainer()
    {
        return $this->setsizesm( 'materialListDisainer' );
    }

    public function actionStagedisainer()
    {
        $id = (int) Yii::$app->request->post( 'id', 0 );
        $stage = (int) Yii::$app->request->post( 'stage', -1 );
        if ( $id ) {
            if ( $model = Zakaz::findOne( $id ) ) {
                $model->stage = $stage;
                if ( $model->update( false, ['stage'] ) ) {
                    if ( $this->checkPechatnikOnStageChange( $id, $stage ) ) {
                        return ['status' => 'ok', 'pechatnikTable' => 'hasChange'];
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
            return ['status' => 'error', 'errorText' => 'Неверное значение ID или STAGE'];
        }
    }

    public function actionListdisainer()
    {
        if ( ($page = (int) Yii::$app->request->post( 'page', 0 )) < 0 )
                $page = 0;
        $rVal = [
            'list'       => [],
            'hidden'     => [],
            'attention'  => [],
            'colOptions' => $this->getOptions( true, 'materialListDisainer' ),
        ];
        $rVal['pageSize'] = (int) (isset( $rVal['colOptions']['pageSize'] ) ? $rVal['colOptions']['pageSize'] : 10);
        $colsN = $this->getCollName( $rVal['colOptions'] );
        $queryPrepary = Zakaz::find()->where( ['stage' => array_keys( \app\models\zakaz\Zakaz::$_stageD )] );
        $this->filterApplay( $queryPrepary, false, true );
        $rVal['count'] = $queryPrepary->count();
        $pageCnt = (int) ceil( $rVal['count'] / $rVal['colOptions']['pageSize'] );
        if ( $page >= $pageCnt ) $page = $pageCnt - 1;
        $rVal['page'] = $page;
        $queryPrepary = Zakaz::find()->where( ['stage' => array_keys( \app\models\zakaz\Zakaz::$_stageD )] )
                ->offset( $page * $rVal['colOptions']['pageSize'] )
                ->limit( $rVal['colOptions']['pageSize'] );
        $this->filterApplay( $queryPrepary, false, true );
        $rVal['tmpSort'] = $this->orderSting;
        $queryPrepary->orderBy( $this->orderSting );
        $query = $queryPrepary->all();
        if ( $query ) {
            $diffCol = array_diff( $colsN, $query[0]->attributes() );
            $opt = $rVal['colOptions'];
            foreach ( $query as $el ) {
                $tmpRv = [];
                foreach ( $colsN as $key )
                    $tmpRv[$key] = $el[$key];
                $rVal['list'][] = $tmpRv;
                $rVal['hidden'][$el['id']] = ['stage' => $el['stage']];
            }
        }
        $rVal['pCnt'] = $pageCnt;
        $rVal['colN'] = $colsN;
        $rVal['sortable'] = ['dateofadmissionText'];
        $rVal['filters'] = [
            'stageText' => [
                'realColName' => 'stage',
                'content'     => \app\models\zakaz\Zakaz::$_stageD
            ],
        ];
        $rVal['stageLevels'] = Zakaz::$_stage;
        return $rVal;
    }

}
