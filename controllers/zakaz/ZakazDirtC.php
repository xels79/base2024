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
use \app\models\zakaz\ZakazDirt;

//use app\models\tables\Worktypes;

/**
 * Description of ZakazDirt
 *
 * @author Александр
 */
class ZakazDirtC extends ZakazProizvodstvo {

    public static $availableColumnsDirt = [
        'dateofadmissionText',
        'zak_idText',
        'ourmanagername',
        'total_coastText',
        'name',
        'production_idText',
        'number_of_copiesText',
        'number_of_copies1Text',
    ];

    public function behaviors()
    {
        return ArrayHelper::merge( parent::behaviors(), [
                    'verbs' => [
                        'class'   => VerbFilter::className(),
                        'actions' => [
                            'dirtindex'               => ['get'],
                            'setsizesmdirt'           => ['post'],
                            'getavailablecolumnsdirt' => ['post'],
                            'setcolumnsdirt'          => ['post'],
                            'listdirt'                => ['post'],
                        ]
                    ]
        ] );
    }

    public function actionDirtindex()
    {
        $this->layout = 'main_2.php';
        return $this->render( 'dirtindex' );
    }

    public function actionGetavailablecolumnsdirt()
    {
        return $this->availabelColumns( 'materialListDirt', self::$availableColumnsDirt );
    }

    public function actionSetcolumnsdirt()
    {
        return $this->setcolumns( 'materialListDirt' );
    }

    public function actionSetsizesdirt()
    {
        return $this->setsizesm( 'materialListDirt' );
    }

    public function actionListdirt()
    {
        if ( ($page = (int) Yii::$app->request->post( 'page', 0 )) < 0 )
                $page = 0;
        $rVal = [
            'list'       => [],
            'hidden'     => [],
            'attention'  => [],
            'colOptions' => $this->getOptions( true, 'materialListDirt' ),
        ];
        $rVal['pageSize'] = (int) (isset( $rVal['colOptions']['pageSize'] ) ? $rVal['colOptions']['pageSize'] : 10);
        $colsN = $this->getCollName( $rVal['colOptions'] );
        $queryPrepary = ZakazDirt::find();
        $this->filterApplay( $queryPrepary, false, true );
        $rVal['count'] = $queryPrepary->count();
        $pageCnt = (int) ceil( $rVal['count'] / $rVal['colOptions']['pageSize'] );
        if ( $page >= $pageCnt ) $page = $pageCnt - 1;
        $rVal['page'] = $page;
        $queryPrepary = ZakazDirt::find()
                ->offset( $page * $rVal['colOptions']['pageSize'] )
                ->limit( $rVal['colOptions']['pageSize'] );
        $this->filterApplay( $queryPrepary, false, true );
        $rVal['tmpSort'] = $this->orderSting;
        $queryPrepary->orderBy( $this->orderSting );
//        $queryPrepary->groupby('id');
        //$queryPrepary->select('zakaz_dirt.id as id,dateofadmission,name,ourmanager_id,manager_id,production_id,worktypes_id,total_coast');
        $query = $queryPrepary->all();
        if ( $query ) {
            $diffCol = array_diff( $colsN, $query[0]->attributes() );
            $opt = $rVal['colOptions'];
            foreach ( $query as $el ) {
                $tmpRv = [];
                foreach ( $colsN as $key )
                    $tmpRv[$key] = $el[$key];
                $rVal['list'][] = $tmpRv;
                //$rVal['hidden'][$el['id']]=['stage'=>$el['stage'],'ourmanager_id'=>$el['ourmanager_id']];
            }
        }
        $rVal['pCnt'] = $pageCnt;
        $rVal['colN'] = $colsN;
        $rVal['sortable'] = ['dateofadmissionText'];
        $rVal['filters'] = [];
        return $rVal;
    }

}
