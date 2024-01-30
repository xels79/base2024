<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 *
 * Сводная информация по Фирмам
 *
 */

namespace app\controllers\admin;

/**
 * Description of FirmsController
 *
 * @author Александр
 */
use yii;
use app\controllers\ControllerMain;
use yii\filters\VerbFilter;
use app\controllers\zakaz\ZakazListBaseFunction;
use yii\helpers\ArrayHelper;

class FirmsController extends ZakazListBaseFunction {

    const titles = [
        'Zak'  => 'Заказчики',
        'Pod'  => 'Подрядчики',
        'Post' => 'Поставщики'
    ];
    const buttonsText = [
        'Zak'  => 'клиент',
        'Pod'  => 'подрядчик',
        'Post' => 'поставщик'
    ];
    const captionText = [
        'Zak'  => 'Cписок заказчиков',
        'Pod'  => 'Cписок подрядчиков',
        'Post' => 'Cписок поставщиков'
    ];

    public $firmClassName;

    public function init()
    {

        if ( !($this->firmClassName = Yii::$app->request->get( 'firmclassname', false )) )
                $this->firmClassName = Yii::$app->request->post( 'firmclassname', 'Zak' );
        $this->formClassName = 'app\models\admin\\' . $this->firmClassName;
        $this->cacheName = $this->firmClassName . 'list-opt-cache';
        $this->defaultCollName = 'firm_id';
        self::$availableColumns = [
            'mainName',
            'statusText',
            'mainFormText',
            'typeOfPaymentText',
            'has_contractText',
            'contract_number',
            'delay',
            'contact1Name',
            'phone',
            'mail',
            'woptype',
            'additional'
        ];
        if ( $this->firmClassName === 'Zak' ) {
            self::$availableColumns[] = 'blackList';
        }
        parent::init();
    }

    public function beforeAction( $action )
    {
        if ( parent::beforeAction( $action ) ) {
            if ( $action->id === 'index' ) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            }
            return true;
        } else {
            return false;
        }
    }

    public function behaviors()
    {
        return ArrayHelper::merge( parent::behaviors(), [
                    'verbs' => [
                        'class'   => VerbFilter::className(),
                        'actions' => [
                            'index' => ['get'],
                            'list'  => ['post']
                        ]
                    ]
        ] );
    }

    public function actionIndex()
    {
        $this->layout = 'main_2.php';
        return $this->render( 'index', [
                    'title'         => self::titles[$this->firmClassName],
                    'buttonText'    => self::buttonsText[$this->firmClassName],
                    'captionText'   => self::captionText[$this->firmClassName],
                    'firmclassname' => $this->firmClassName
        ] );
    }

    public function actionList()
    {
        if ( ($page = (int) Yii::$app->request->post( 'page', 0 )) < 0 )
                $page = 0;
        $woptype = Yii::$app->request->post( 'woptype' );
        $filters=Yii::$app->request->post('filters',null);
        if (!is_array($filters)) $filters=null;
        $rVal = [
            'list'       => [],
            'hidden'     => [],
            'attention'  => [],
            'colOptions' => $this->getOptions( true, $this->cacheName ),
        ];
        $rVal['pageSize'] = (int) (isset( $rVal['colOptions']['pageSize'] ) ? $rVal['colOptions']['pageSize'] : 10);
        $colsN = $this->getCollName( $rVal['colOptions'] );
        $query = $this->formClassName::find()
                ->offset( $page * $rVal['colOptions']['pageSize'] )
                ->limit( $rVal['colOptions']['pageSize'] );
        if ( $woptype && ($this->firmClassName === 'Post' || $this->firmClassName === 'Pod') ) {
            $query->leftJoin( 'WOP' . $this->firmClassName, 'firm' . $this->firmClassName . '.firm_id=WOP' . $this->firmClassName . '.firm_id' )
                    ->where( ['WOP' . $this->firmClassName . '.referensId' => $woptype] );
        }
        $this->filterApplay($query);
        $rVal['count'] = $query->count();
        $pageCnt = (int) ceil( $rVal['count'] / $rVal['colOptions']['pageSize'] );
        if ( $page >= $pageCnt ) $page = $pageCnt - 1;
        $rVal['page'] = $page;
        $query = $this->formClassName::find()
                ->offset( $page * $rVal['colOptions']['pageSize'] )
                ->limit( $rVal['colOptions']['pageSize'] );
        if ( $woptype && ($this->firmClassName === 'Post' || $this->firmClassName === 'Pod') ) {
            $query->leftJoin( 'WOP' . $this->firmClassName, 'firm' . $this->firmClassName . '.firm_id=WOP' . $this->firmClassName . '.firm_id' )
                    ->where( ['WOP' . $this->firmClassName . '.referensId' => $woptype] );
        }
        $this->filterApplay($query);
        $query = $query->all();
        if ( $query ) {
            $diffCol = array_diff( $colsN, $query[0]->attributes() );
            $opt = $rVal['colOptions'];
            foreach ( $query as $el ) {
                $tmpRv = [];
                foreach ( $colsN as $key ) {
                    if ( $key != 'empt' ) $tmpRv[$key] = $el[$key];
                    else $tmpRv[$key] = '';
                }
                $rVal['list'][] = $tmpRv;
                $rVal['hidden'][$el['firm_id']] = []; //'stage'=>$el['stage'],'ourmanager_id'=>$el['ourmanager_id']];
                if ( $el->canGetProperty( 'blackList' ) ) {
                    $rVal['hidden'][$el['firm_id']]['blackList'] = $el->blackList;
                }
            }
        }
        $rVal['pCnt'] = $pageCnt;
        $rVal['colN'] = $colsN;
        $rVal['cacheName'] = $this->cacheName;
        $rVal['filters']=$this->_filters;
        return $rVal;
    }
    protected function createFilters()
    {
        return [
            'mainName'=> [
                'realColName' => 'mainName',
                'like'        => []
            ],
        ];
    }
}
