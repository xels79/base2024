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

/**
 * Description of ZakazProduct
 *
 * @author xel_s
 */
class ZakazProduct extends ZakazDirtC {

    protected $isproduct = 0;
    public static $availableColumnsMaterial = [
        'deadlineText',
        'dateofadmissionText',
        'ourmanagername',
        'name',
        'number_of_copiesText',
        'number_of_copies1Text',
        'production_idText',
        'material_post_list',
        'material_count_list',
        'material_name_list',
//        'material_info_list',
        'material_ordered',
        'material_delivery',
        'material_info_name',
        'material_info_color',
        'material_info_format',
        'material_info_razmerlista',
        'material_info_density',
//        'stageText'
    ];

    public function behaviors()
    {
        return ArrayHelper::merge( parent::behaviors(), [
                    'verbs' => [
                        'class'   => VerbFilter::className(),
                        'actions' => [
                            'setsizesmp'                    => ['post'],
                            'getavailablecolumnsmaterialsp' => ['post'],
                            'getavailablecolumnsp'          => ['post'],
                            'setcolumnsmp'                  => ['post'],
                            'listmaterialp'                 => ['post'],
                            'stageproizvodsvo'              => ['post']
                        ]
                    ]
        ] );
    }

    public function beforeAction( $action )
    {
        if ( parent::beforeAction( $action ) ) {
            $this->isproduct = (int) Yii::$app->request->post( 'isproduct', (int) Yii::$app->request->get( 'isproduct', 0 ) );
            return true;
        } else {
            return false;
        }
    }

    private function _optNmae()
    {
        return ('materialListP' . ($this->isproduct == 0 ? 'roduct' : 'zakaz'));
    }

    public function actionSetsizesmp()
    {
        return $this->setsizesm( $this->_optNmae() );
    }

    public function actionGetavailablecolumnsmaterialsp()
    {
        return $this->availabelColumns( $this->_optNmae(), self::$availableColumnsMaterial );
    }

    public function actionSetcolumnsmp()
    {
        return $this->setcolumns( $this->_optNmae() );
    }

    public function actionListmaterialp()
    {
        if ( ($page = (int) Yii::$app->request->post( 'page', 0 )) < 0 )
                $page = 0;
//        $isproduct=Yii::$app->request->post('isproduct');
        Yii::debug( $this->_optNmae(), 'test material' );
        Yii::debug( $this->getOptions( true, $this->_optNmae() ), 'test material' );
        $rVal = [
            'list'       => [],
            'hidden'     => [],
            'attention'  => [],
            'colOptions' => $this->getOptions( true, $this->_optNmae() ),
        ];
        $rVal['pageSize'] = (int) (isset( $rVal['colOptions']['pageSize'] ) ? $rVal['colOptions']['pageSize'] : 10);
        $colsN = $this->getCollName( $rVal['colOptions'] );
        $queryPrepary = Zakaz::find()
                ->leftJoin( 'zakaz_materials', 'zakaz_materials.zakaz_id=zakaz.id' )
//                ->groupBy('id')
                ->where( ['and', ['zakaz_materials.supplierType' => 2]] );
        if ( Yii::$app->request->post( 'isproduct' ) !== '1' )
                $queryPrepary->andWhere( ['zakaz_materials.delivery_date' => null] );
        if ( $this->isproduct )
                $queryPrepary->andWhere( ['in', 'stage', self::$proizvodstvoCanChangeSee] );
        $this->filterApplay( $queryPrepary, false, true );
        Yii::debug( \yii\helpers\VarDumper::dumpAsString( $queryPrepary->asArray() ), 'queryPrepary' );
        $rVal['count'] = $queryPrepary->count();
        $pageCnt = (int) ceil( $rVal['count'] / $rVal['colOptions']['pageSize'] );
        if ( $page >= $pageCnt ) $page = $pageCnt - 1;
        $rVal['page'] = $page;
        $queryPrepary = Zakaz::find()
                ->offset( $page * $rVal['colOptions']['pageSize'] )
                ->limit( $rVal['colOptions']['pageSize'] )
                ->leftJoin( 'zakaz_materials', 'zakaz_materials.zakaz_id=zakaz.id' )
                ->where( ['=', 'zakaz_materials.supplierType', 2] ); //['and',['zakaz_materials.delivery_date'=>null],
        if ( Yii::$app->request->post( 'isproduct' ) !== '1' )
                $queryPrepary->andWhere( ['zakaz_materials.delivery_date' => null] );
        if ( $this->isproduct )
                $queryPrepary->andWhere( ['in', 'stage', self::$proizvodstvoCanChangeSee] );

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
                if ( $el->attention && mb_strlen( $el->attention ) ) {
                    $rVal['attention'][(int) $el->id] = $el->attention;
                }
                $rVal['list'][] = $tmpRv;
                $rVal['hidden'][$el['id']] = ['stage' => $el['stage'], 'ourmanager_id' => $el['ourmanager_id']];
            }
        }
        $rVal['pCnt'] = $pageCnt;
        $rVal['colN'] = $colsN;
        $rVal['sortable'] = ['id', 'dateofadmissionText', 'deadlineText'];
        $rVal['filters'] = $this->_filters;
        $rVal['stageLevels'] = Zakaz::$_stage;
        return $rVal;
    }

    public function actionStageproizvodsvo()
    {
        $id = (int) Yii::$app->request->post( 'id', 0 );
        $stage = (int) Yii::$app->request->post( 'stage', -1 );
        if ( $id && $stage > -1 && ($stage === 3 || $stage === 5 || $stage === 6 || $stage === 8) ) {
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

}
