<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz;

/**
 * Description of ZakazListBaseFunction
 *
 * @author Александр
 */
use Yii;
use app\controllers\zakaz\Zakazbase;
use yii\filters\VerbFilter;
use app\components\MyHelplers;
use yii\helpers\ArrayHelper;
use app\models\Options;
use app\models\tables\DependTables;
use app\models\tables\Pechiatnik;

/*
 * @formClassName   -- алиас до основного класса
 * @cacheName       -- имя переменной кэша
 * @availableColumns-- доступные колонки должны быть добавлены в init()
 * @defaultCollName -- колонка по умолчанию
 */

class ZakazListBaseFunction extends Zakazbase {

    public $formClassName = '';
    public $cacheName = '';
    public static $availableColumns = [];
    public $defaultCollName = 'id';
    public $filters;
    private static $_filters = null;
    private $_cacheQueryParam = null;
    private $_getPostist = null;

    private function postFirmList()
    {
        if ( $this->_getPostist === null ) {
            $this->_getPostist = ArrayHelper::map(
                            \app\models\admin\Post::find()
                                    ->select( ['firm_id', 'mainName'] )
                                    ->all(),
                            'firm_id', 'mainName' );
        }
        return $this->_getPostist;
    }

    private $_getPodtist = null;

    private function podFirmList()
    {
        if ( $this->_getPodtist === null ) {
            $this->_getPodtist = ArrayHelper::map(
                            \app\models\admin\Pod::find()
                                    ->select( ['firm_id', 'mainName'] )
                                    ->all(),
                            'firm_id', 'mainName' );
        }
        return $this->_getPodtist;
    }

    protected function createFilters()
    {
        //Фильтр:
        //Структура $realColName:
        //Имя колонки в таблице zakaz или (разделитель точка):
        //'подключамая таблица.ключ столб в ней.сноска в табл zakaz' или
        //'подключамая таблица.ключ столб в ней.сноска в табл zakaz.колонка для сравнения в подключ табл'
        //если к имени подключаемой таблицы добавить '!' в начале операция
        //joinLeft не будет добовлятся.

        return [
            'method_of_payment_text'         => [
                'realColName' => 'account_number',
                'like'        => []
            ],
            'worktypes_id_text'              => [
                'realColName' => 'worktypes_id',
                'content'     => ArrayHelper::map(
                        \app\models\tables\Worktypes::find()->orderBy( 'name' )->asArray()->all(),
                        'id', 'name' )
            ],
            'material_firms'                 => [
                'realColName' => 'zakaz_materials.zakaz_id.`zakaz.id`.firm_id',
                'andWhere'    => 'zakaz_materials.supplierType=2',
                'content'     => $this->postFirmList()
            ],
            'podryad_name_list'              => [
                'realColName' => 'zakaz_pod.zakaz_id.`zakaz.id`.pod_id',
                'content'     => $this->podFirmList()
            ],
            'podryad_total_coast_list'       => [
                'realColName' => 'zakaz_pod.zakaz_id.`zakaz.id`.pod_id',
                'content'     => $this->podFirmList()
            ],
            'production_idText'              => [
                'realColName' => 'production_id',
                'content'     => ArrayHelper::map(
                        \app\models\tables\Productions::find()
                                ->select( ['id', 'name'] )
                                ->orderBy( 'name' )
                                ->all(),
                        'id', 'name' )
            ],
            'ourmanagername'                 => [
                'realColName' => 'ourmanager_id',
                'content'     => ArrayHelper::map(
                        \app\models\TblUser::find()
                                ->select( ['id', 'realname'] )
                                ->orderBy( 'realname' )
                                ->all(),
                        'id', 'realname' )
            ],
            'zak_idText'                     => [
                'realColName' => 'firmZak.firm_id.zak_id',
                'content'     => ArrayHelper::map(
                        \app\models\admin\Zak::find()
                                ->select( ['firm_id', 'mainName'] )
                                ->orderBy( 'mainName' )
                                ->all(),
                        'firm_id', 'mainName' )
            ],
            'material_post_list'             => [
                'realColName' => 'zakaz_materials.zakaz_id.`zakaz.id`.firm_id',
                'andWhere'    => 'zakaz_materials.supplierType=2',
                'content'     => $this->postFirmList()
            ],
            'material_total_coast_list'      => [
                'realColName' => 'zakaz_materials.zakaz_id.`zakaz.id`.firm_id',
                'content'     => $this->postFirmList()
            ],
            'stageText'                      => [
                'realColName' => 'stage',
                'content'     => \app\models\zakaz\Zakaz::$_stage
            ],
            'material_residue_list'          => [
                'realColName' => 'material_residue_list',
                'content'     => [1 => 'Не оплаченные']
            ],
            'podryad_residue_list'           => [
                'realColName' => 'podryad_residue_list',
                'content'     => [1 => 'Не оплаченные']
            ],
            'invoice_from_this_company_text' => [
                'realColName'     => 'invoice_from_this_company',
                'filterCalculate' => function($val) {
                    if ( is_array( $val ) ) {
                        $newVal = [];
                        foreach ( array_keys( $val ) as $i ) {
                            if ( $val[$i] > 999 ) $newVal[] = $val[$i] - 1000;
                        }
                        return $newVal;
                    } else {
                        return (int) $val - 1000;
                    }
                },
                'orFilterVar' => 'zakaz.method_of_payment',
                'content'     => ArrayHelper::merge( [1000 => 'Договорная', 1002 => 'В/З'], ArrayHelper::map(
                                \app\models\admin\RekvizitOur::find()
                                        ->select( ['rekvizit_id', 'name'] )
                                        ->asArray()
                                        ->all(),
                                'rekvizit_id', 'name' ) )
            ],
            'oplata_status_text'    => [
                'realColName' => 'oplata_status',
                'content'     => ['Не оплачен', 'Предоплата', 'Оплачен', 'Переплата']
            ],
            'division_of_work_text' => [
                'realColName' => 'division_of_work',
                'content'     => ['50/50', '100%', '0%']
            ],
            'other_spends_list'     => [
                'realColName' => 'other_spends_list',
                'content'     => [
                    'exec_bonus'     => 'Бонус',
                    'exec_delivery'  => 'Доставка',
                    'exec_markup'    => 'Наценка',
                    'exec_speed'     => 'Срочность',
                    'exec_transport' => 'Транспорт'
                ]
            ],
            'dateofadmissionText'   => [
                'realColName' => 'dateofadmission',
                'isDate'      => true
            ],
            'deadlineText'          => [
                'realColName' => 'deadline',
                'isDate'      => true
            ]
        ];
    }

    public function init()
    {
        parent::init();
        if ( !$this->cacheName ) {
            $this->cacheName = $this->formClassName . '-cache';
        }
        if ( self::$_filters === null )
                self::$_filters = $this->createFilters();
        $this->filters = Yii::$app->request->post( 'filters', null );
    }

    public function beforeAction( $action )
    {
        if ( parent::beforeAction( $action ) ) {
            if ( in_array( $action->id, ['index', 'view', 'materialindex', 'dirtindex',
                        'disainerindex', 'proizvodstvoindex', 'bugalterindex', 'bugalterpostindex',
                        'bugalterpodryadindex'] ) ) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            }
            return true;
        } else {
            return false;
        }
    }

    protected function setsizesm( $keyName )
    {
        $options = Yii::$app->request->post( 'options' );
        if ( $options && is_array( $options ) ) {
            $tmpOpt = $this->getOptions( true, $keyName );
            $this->searchWidthAndSet( $options, $tmpOpt['constant'] );
            $this->searchWidthAndSet( $options, $tmpOpt['add'] );
            Yii::$app->cache->delete( $this->getCacheOptionKey( $keyName ) );
            $model = $this->getOptionsModel( $keyName );
            $model->options = $tmpOpt;
            if ( $model->save() )
                    return ['status'  => 'ok', 'messgae' => 'Options - сохранен.',
                    'reqOpt'  => $options, 'tmpOpt'  => $tmpOpt];
            else
                    return ['status'    => 'error', 'errorText' => 'Ошибка сохранения',
                    'errors'    => $model->errors];
        } else {
            return ['status' => 'error', 'errorText' => 'Не переданны параметры!'];
        }
    }

    public function get_filters()
    {
        /*
          if ($this->filters && array_key_exists('!zakaz_materials.zakaz_id.id.type_id', $this->filters)){
          $types= \app\models\tables\Tablestypes::find()->where(['id'=>$this->filters['!zakaz_materials.zakaz_id.id.type_id']])->all();
          if (!count($types)) return self::$_filters;
          $tmp=[];
          foreach ($types as $mat){
          $name=$mat->translit($mat->name);
          $struct= DependTables::dependsTablesNamesFromRus($name, \yii\helpers\Json::decode($mat->struct));
          $i=count($struct)-1;
          $query= \app\models\tables\DependTable::createObject($struct[$i])->find();
          //$select=[$struct[$i].'.name as name'.$i];
          $select=[$struct[$i].'.id as id'];
          $prev=$struct[$i--];
          $namePos=0;
          for (;$i>-1;){
          $query->join('LEFT OUTER JOIN',$struct[$i],$struct[$i].'.'.DependTables::$pKey.'='.$prev.'.'.DependTables::$reference);
          if (strpos($struct[$i], 'names')!==false){
          $select[]=$struct[$i].'.name as name'.$i;
          $namePos=$i;
          }
          $prev=$struct[$i--];
          }
          $query->select($select);
          $result=ArrayHelper::map($query->asArray()->all(),'id','name'.$namePos);
          $tmpResult=[];
          foreach ($result as $k=>$v){
          $tmpKey=$mat->id.'_'.$v;
          if (array_key_exists($tmpKey, $tmpResult)){
          $tmpResult[$tmpKey][strlen($tmpResult[$tmpKey])-1]=',';
          $tmpResult[$tmpKey].=$k.']';
          } else {
          $tmpResult[$tmpKey]='['.$k.']';
          }
          }
          foreach ($tmpResult as $k=>$v){
          $tmpKey= explode('_', $k);
          $tmp[$tmpKey[0].'_'.$v]=$tmpKey[1];
          }
          }
          return ArrayHelper::merge([
          'material_info_name'=>[
          'realColName'=>'!zakaz_materials.zakaz_id.id.mat_id',
          'content'=> $tmp
          ]
          ],self::$_filters);
          }else
         *
         */
        return self::$_filters;
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'setsizes'            => ['post'],
                    'getavailablecolumns' => ['post'],
                    'setcolumns'          => ['post'],
                ]
            ]
        ];
    }

    protected function getCacheOptionKey( $name = null )
    {
        $name = $name ? $name : $this->cacheName;
        return MyHelplers::hashString( Yii::$app->user->identity->id . $name . Yii::$app->id );
    }

    protected function getOptionsModel( $name = null )
    {
        $name = $name ? $name : $this->cacheName;
        $modelZakaz = new $this->formClassName();
        if ( !$model = Options::find()->where( [
                    'userid'   => (int) Yii::$app->user->identity->id,
                    'optionid' => $name
                ] )->one() ) {
            $model = new Options();
            $model->userid = (int) Yii::$app->user->identity->id;
            $model->optionid = $name;
            $model->options = [
                'constant' => [
                    ['name' => $this->defaultCollName, 'width' => 30, 'label' => $modelZakaz->getAttributeLabel( $this->defaultCollName )],
                ],
                'add'      => [
                   // ['name' => 'empt', 'width' => 30, 'label' => '']
                ],
                'pageSize' => 10
            ];
            $model->save();
        }
        return $model;
    }

    protected function getCollName( &$opt )
    {
        Yii::trace( \yii\helpers\VarDumper::dumpAsString( $opt ), 'getCollName' );
        return ArrayHelper::merge( array_map( function($el) {
                            return $el['name'];
                        }, $opt['constant'] )
                        , array_map( function($el) {
                            return $el['name'];
                        }, $opt['add'] ) );
    }

    public function searchWidthAndSet( &$options, &$arrOfVals )
    {
        foreach ( $arrOfVals as $key => $el ) {
            if ( isset( $options[$el['name']] ) )
                    $arrOfVals[$key]['width'] = $options[$el['name']]['width'];
        }
    }

    public function actionSetsizes()
    {
        $options = Yii::$app->request->post( 'options' );
        if ( $options && is_array( $options ) ) {
            $tmpOpt = $this->getOptions( true, $this->cacheName );
            $this->searchWidthAndSet( $options, $tmpOpt['constant'] );
            $this->searchWidthAndSet( $options, $tmpOpt['add'] );
            Yii::$app->cache->delete( $this->getCacheOptionKey() );
            $model = $this->getOptionsModel();
            $model->options = $tmpOpt;
            if ( $model->save() )
                    return ['status'  => 'ok', 'messgae' => 'Options - сохранен.',
                    'reqOpt'  => $options, 'tmpOpt'  => $tmpOpt];
            else
                    return ['status'    => 'error', 'errorText' => 'Ошибка сохранения',
                    'errors'    => $model->errors];
        } else {
            return ['status' => 'error', 'errorText' => 'Не переданны параметры!'];
        }
    }

    protected function findPosInSArray( $key, &$arr )
    {
        $cnt = count( $arr );
        $key = strtolower( $key );
        for ( $i = 0; $i < $cnt; $i++ ) {
            if ( strtolower( $arr[$i]['name'] ) === $key ) return $i;
        }
        return false;
    }

    protected function setcolumns( $name = null )
    {
        $name = $name ? $name : $this->cacheName;
        if ( $colToAdd = Yii::$app->request->post( 'coltoadd' ) ) {
            $showAttentionColumn = Yii::$app->request->post( 'showAttentionColumn' ) === 'true';
            $colors = Yii::$app->request->post( 'colors' );
            $pageSize = (int) Yii::$app->request->post( 'pageSize', 10 );
            if ( !is_array( $colToAdd ) ) {
                if ( $colToAdd === 'remove-all' ) $colToAdd = [];
                else
                        return ['status' => 'error', 'errorText' => 'Не верное значение колонки colToAdd'];
            }
            $modelZakaz = new $this->formClassName();
            $model = $this->getOptionsModel( $name );
            $tmpOpt = $model->getOptions( true, $name );
            $newOpt = ['add' => [], 'constant' => $tmpOpt['constant']];
            foreach ( $colToAdd as $el ) {
                $pos = $this->findPosInSArray( $el, $tmpOpt['add'] );
                if ( $pos === false )
                        $newOpt['add'][] = ['name' => $el, 'width' => 30, 'label' => $modelZakaz->getAttributeLabel( $el )];
                elseif ( in_array( $tmpOpt['add'][$pos]['name'], $colToAdd ) !== false )
                        $newOpt['add'][] = $tmpOpt['add'][$pos];
            }

            $newOpt['pageSize'] = (int) Yii::$app->request->post( 'pagesize', 10 );
//            if ( $pos = $this->findPosInSArray( 'empt', $tmpOpt['add'] ) ) {
//                $newOpt['add'][] = $tmpOpt['add'][$pos];
//            } else {
//                $newOpt['add'][] = ['name' => 'empt', 'width' => 30, 'label' => ''];
//            }
            if ( $colors ) $newOpt['colors'] = $colors;
            $newOpt['pageSize'] = $pageSize;
            $newOpt['showAttentionColumn'] = $showAttentionColumn;
            $model->options = $newOpt;

            if ( $model->save() ) {
                Yii::$app->cache->delete( $this->getCacheOptionKey( $name ) );
                return ['status'  => 'ok', 'messgae' => 'Options - сохранен.', 'opt'     => $newOpt,
                    'post'    => Yii::$app->request->post()];
            } else
                    return ['status'    => 'error', 'errorText' => 'Ошибка сохранения',
                    'errors'    => $model->errors];
        } else {
            return ['status' => 'error', 'errorText' => 'Не переданны колонки'];
        }
    }

    public function actionSetcolumns()
    {
        return $this->setcolumns();
    }

    protected function availabelColumns( $name = null, $arr = null )
    {
        $name = $name ? $name : $this->cacheName;
        $arr = $arr ? $arr : self::$availableColumns;
        $model = new $this->formClassName();
        $rVal = ['status' => 'ok', 'availableColumns' => [], 'options' => $this->getOptions( true, $name )];
        foreach ( $arr as $el ) {
            $rVal['availableColumns'][$el] = $model->getAttributeLabel( $el );
        }
        return $rVal;
    }

    public function actionGetavailablecolumns()
    {
        return $this->availabelColumns();
    }

    public function getOrderSting($default=[])
    {
        $sort = array_merge($default,Yii::$app->request->post( 'sort', [] ));
        $rVal = '';
        foreach ( $sort as $key => $val ) {
            if ( $key != 'material_post_list' ) {
                $rVal .= ($rVal ? ',' : '');
                $rVal .= str_replace( 'Text', '', $key ) . ' ' . $val;
            }
        }
        if ( !$rVal ) {
            $rVal = 'id ASC';
        }
        return $rVal;
    }

    private function _dateFilterApplay()
    {
        $dateFrom = Yii::$app->request->post( 'date-from' );
        $dateTo = Yii::$app->request->post( 'date-to' );
        if ( $dateFrom && !$dateTo ) {
            $dateTo = date( 'd.m.y', time() );
        }
        if ( $dateFrom || $dateTo ) {
            $dfParam = ['>=', 'dateofadmission', Yii::$app->formatter->asDate( $dateFrom, 'php:Y-m-d' )];
            $dtParam = ['<=', 'dateofadmission', Yii::$app->formatter->asDate( $dateTo, 'php:Y-m-d' )];
            if ( $dateFrom ) {
                $this->_cacheQueryParam['where2'][] = $dfParam;
                if ( $dateTo ) $this->_cacheQueryParam['where2'][] = $dtParam;
            } else {
                $this->_cacheQueryParam['where2'][] = $dtParam;
            }
            Yii::debug( 'from: ' . Yii::$app->formatter->asDate( $dateFrom, 'php:Y-m-d' ) . ' to:' . Yii::$app->formatter->asDate( $dateTo, 'php:Y-m-d' ), 'filters' );
        }
    }

    private function _filterApplay( $query, $first )
    {
        if ( count( $this->_cacheQueryParam['tables'] ) ) {
            //$query->leftJoin($this->_cacheQueryParam['tables'], $this->_cacheQueryParam['on']);
            if ( count( $this->_cacheQueryParam['tables'] ) === count( $this->_cacheQueryParam['on'] ) ) {
                if ( $query->join === null ) $query->join = [];
                for ( $i = 0; $i < count( $this->_cacheQueryParam['tables'] ); $i++ ) {
                    if ( !in_array( $this->_cacheQueryParam['tables'][$i], ArrayHelper::getColumn( $query->join, 1 ) ) )
                            $query->leftJoin( $this->_cacheQueryParam['tables'][$i], $this->_cacheQueryParam['on'][$i] );
                }
            } else {
                new \yii\web\ServerErrorHttpException( 'on<table' );
            }
        }
        if ( count( array_keys( $this->_cacheQueryParam['where'] ) ) ) {
            if ( $first ) {
                $query->where( $this->_cacheQueryParam['where'] );
                $first = false;
            } else {
                $query->andWhere( $this->_cacheQueryParam['where'] );
            }
        }
//        if (count($this->_cacheQueryParam['select'])){
//            $query->addSelect($this->_cacheQueryParam['select']);
//        }
        foreach ( $this->_cacheQueryParam['where2'] as $el ) {
            if ( $first ) {
                if ( is_array( $el ) && array_key_exists( 'where', $el ) ) {
                    $query->where( $el['where'] );
                    $query->addParams( $el['params'] );
                    $first = false;
                } else {
                    $query->where( $el );
                    $first = false;
                }
            } else {
                if ( is_array( $el ) && array_key_exists( 'where', $el ) ) {
                    $query->andWhere( $el['where'] );
                    $query->addParams( $el['params'] );
                } else {
                    $query->andWhere( $el );
                }
            }
        }
        return $query;
    }

    //material_residue_list
    private function _filter_material_residue_list_applay( $el )
    {
        Yii::debug( 'filter_residue_list_applay:start', 'filters' );
        if ( array_key_exists( $el['realColName'], $this->filters ) ) {
            if ( count( $this->filters[$el['realColName']] ) > 1 || (count( $this->filters[$el['realColName']] ) && $this->filters[$el['realColName']][0] == 1) ) {
                $this->_cacheQueryParam['where2'][] = 'zakaz_materials.count*zakaz_materials.coast>zakaz_materials.paid';
                Yii::debug( 'filter_residue_list_applay:aplay', 'filters' );
            }
        }
        Yii::debug( 'filter_residue_list_applay:end', 'filters' );
    }

    private function _filter_podryad_residue_list_applay( $el )
    {
        Yii::debug( 'filter_podryad_residue_list_applay:start', 'filters' );
        if ( array_key_exists( $el['realColName'], $this->filters ) ) {
            if ( count( $this->filters[$el['realColName']] ) > 1 || (count( $this->filters[$el['realColName']] ) && $this->filters[$el['realColName']][0] == 1) ) {
                $this->_cacheQueryParam['where2'][] = 'zakaz_pod.payment>zakaz_pod.paid';
                Yii::debug( 'filter_podryad_residue_list_applay:aplay', 'filters' );
            }
        }
        Yii::debug( 'filter_podryad_residue_list_applay:end', 'filters' );
    }

    private function _filter_oplataText_applay( $el )
    {
        if ( array_key_exists( $el['realColName'], $this->filters ) ) {
            $this->_cacheQueryParam['tables'][] = '(select zakaz_id, sum(summ) as spand from `zakaz_oplata` GROUP BY zakaz_id)as op';
            //if (count($this->_cacheQueryParam['on'])) $this->_cacheQueryParam['on'];
            $this->_cacheQueryParam['on'][] = 'zk.id=op.zakaz_id';
            $this->_cacheQueryParam['where']['`isPredoplata`'] = $this->filters[$el['realColName']];
            $this->_cacheQueryParam['select'][] = '(CASE WHEN total_coast>spand and not spand=0 THEN 1 ELSE CASE WHEN total_coast=spand THEN 3 ELSE 2 END END) AS `isPredoplata`';
        }
    }

    private function _filter_method_of_payment_text_applay( $el )
    {
        if ( array_key_exists( $el['realColName'], $this->filters ) ) {
            //$this->_cacheQueryParam['where2'][]=$el['realColName'].'='.$this->filters[$el['realColName']];//['like',$el['realColName'],$this->filters[$el['realColName']]];
            //$this->_cacheQueryParam['where2'][]=[$el['realColName']=>$this->filters[$el['realColName']],['=',$el['realColName'],'MOPTDOP(zakaz.method_of_payment)']];
            $v = 10000;
            if ( mb_strlen( $this->filters[$el['realColName']] ) > 1 )
                    if ( mb_strpos( 'ДОГОВОРНАЯ', mb_strtoupper( $this->filters[$el['realColName']] ) ) > -1 )
                        $v = 0;
                elseif ( mb_strpos( 'В/З', mb_strtoupper( $this->filters[$el['realColName']] ) ) > -1 )
                        $v = 2;
            if ( $v > 2 && (mb_strpos( 'НЕ ВЫСТАВЛЕН', mb_strtoupper( $this->filters[$el['realColName']] ) ) > -1) ) {
                $v = 1;
                $this->_cacheQueryParam['where2'][] = [
                    'where' => [
                        'zakaz.method_of_payment' => $v,
                        'zakaz.account_number'    => ''
                    ],
                ];
            } else {
                $this->_cacheQueryParam['where2'][] = [
                    'where'  => ['or', $el['realColName'] . '=:FTEXT', 'zakaz.method_of_payment=:MOPTDOP'],
                    'params' => [':MOPTDOP' => $v, ':FTEXT' => $this->filters[$el['realColName']]]
                ];
            }
//            \yii\helpers\VarDumper::dump([
//                'post'=>Yii::$app->request->post(),
//                'f'=>$this->_cacheQueryParam
//            ],10,true);YiiL::$app->and();
        }
    }

    private function other_spends_listNextOR( &$arr, $i )
    {
        if ( $arr[$i] === 'exec_transport' ) {
            $not = ['or', ['not', [$arr[$i] . '_payment' => 0]], ['not', [$arr[$i] . '2_payment' => 0]]];
        } else {
            $not = ['not', [$arr[$i] . '_payment' => 0]];
        }
        if ( $i < count( $arr ) - 1 ) {
            return ['or', ['and', [$arr[$i] => true], $not], $this->other_spends_listNextOR( $arr, $i + 1 )];
        } else {
            return ['and', [$arr[$i] => true], $not];
        }
    }

    private function _filter_other_spends_list_applay( $el )
    {
        if ( array_key_exists( $el['realColName'], $this->filters ) ) {
            if ( count( $this->filters[$el['realColName']] ) > 0 ) {
                $this->_cacheQueryParam['where2'][] = $this->other_spends_listNextOR( $this->filters[$el['realColName']], 0 );
            }
        }
    }

    private function mExplode( $d, $str )
    {
        $rVal = [];
        $l = strlen( $str );
        $tmp = '';
        $sheeld = false;
        for ( $i = 0; $i < $l; $i++ ) {
            if ( $str[$i] === '`' ) {
                $sheeld = !$sheeld;
            } else {
                if ( !$sheeld && $str[$i] === $d ) {
                    $rVal[] = $tmp;
                    $tmp = '';
                } else {
                    $tmp .= $str[$i];
                }
            }
        }
        $rVal[] = $tmp;
        return $rVal;
    }

    public function filterGetCount( &$query )
    {
        $this->filterApplay( $query, false, true );
        if ( !$this->filters || array_key_exists( 'oplataText', $this->filters ) ) {
            return 1;
        } else {
            return $query->count();
        }
        //return $query->count(implode(',', ArrayHelper::merge([0=>'*'], array_slice ($this->_cacheQueryParam['select'],1))));
    }

    public function filterApplay( &$query, $first = true, $notConect = false )
    {
        if ( $this->_cacheQueryParam ) {
            Yii::trace( 'getFromCache', 'Filters' );
            return $this->_filterApplay( $query, $first );
        }
        $this->_cacheQueryParam = ['tables' => [], 'on'     => [], 'where'  => [
            ], 'where2' => [
            ], 'select' => ['zakaz.*']];
        $this->_dateFilterApplay();
        if ( !is_array( $this->filters ) ) {
            return $this->_filterApplay( $query, $first );
        }
        $joined = [];
        Yii::trace( 'BildDate', 'Filters' );
        $subKeyID = 0;
        foreach ( $this->get_filters() as $key => $el ) {
            $tmpKey = '_filter_' . $key . '_applay';
            if ( $this->hasMethod( $tmpKey ) ) {
                $this->$tmpKey( $el );
            } else
            if ( array_key_exists( $el['realColName'], $this->filters ) ) {
                $colNameParam = $this->mExplode( '.', $el['realColName'] );
                $realColName = $el['realColName'];
                Yii::debug( $el, 'Filters_step' );
                Yii::debug( $colNameParam, 'Filters_step' );
                if ( count( $colNameParam ) > 2 ) {
                    if ( !in_array( $colNameParam[0], $joined ) ) {
                        $onStr = $colNameParam[0] . '.' . $colNameParam[1] . '=' . $colNameParam[2];
                        if ( $colNameParam[0][0] != '!' ) {
                            $joined[] = $colNameParam[0];
                            $this->_cacheQueryParam['tables'][] = $colNameParam[0];
                            $this->_cacheQueryParam['on'][] = $onStr;
                        } else {
                            $colNameParam[0] = substr( $colNameParam[0], 1 );
                            $joined[] = $colNameParam[0];
                            $this->_cacheQueryParam['tables'][] = $colNameParam[0];
                            $this->_cacheQueryParam['on'][] = $onStr;
                        }
                    }
                    if ( count( $colNameParam ) > 3 ) {
                        if ( strcmp( $colNameParam[3], 'mat_id' ) != 0 ) {
                            $subKey = $colNameParam[0] . '.' . $colNameParam[3];
                            $subVal = $this->filters[$el['realColName']];
                            //$this->_cacheQueryParam['where'][$colNameParam[0].'.'.$colNameParam[3]]=$this->filters[$el['realColName']];
                        } else {
                            $tmpValues = [];
                            foreach ( $this->filters[$el['realColName']] as $f1 ) {
                                $tmpFilters = explode( '_', $f1 );
                                $tmpFilters[1] = \yii\helpers\Json::decode( $tmpFilters[1] );
                                Yii::trace( \yii\helpers\VarDumper::dumpAsString( $tmpFilters ), 'Filters step' );
                                if ( count( $tmpFilters ) > 1 )
                                        $tmpValues = ArrayHelper::merge( $tmpValues, $tmpFilters[1] );
                            }
                            $subKey = $colNameParam[0] . '.' . $colNameParam[3];
                            $subVal = $tmpValues;
                            //$this->_cacheQueryParam['where'][$colNameParam[0].'.'.$colNameParam[3]]=$tmpValues;
                        }
                    } else {
                        $subKey = $colNameParam[0] . '.' . $colNameParam[1];
                        $subVal = $this->filters[$el['realColName']];
                        //$this->_cacheQueryParam['where'][$colNameParam[0].'.'.$colNameParam[1]]=$this->filters[$el['realColName']];
                    }
                    $this->__endOfPrepare( $subKey, $subVal, $el );
                } elseif ( count( $colNameParam ) !== 1 ) {
                    throw new yii\web\BadRequestHttpException( 'Неверный параметр таблица.колонка.колонка(связанная)' . \yii\helpers\VarDumper::dumpAsString( $colNameParam ) );
                } else {
                    $subKey = $el['realColName'];
                    if ( array_key_exists( 'isDate', $el ) ) {
                        $subVal = Yii::$app->formatter->asDate( $this->filters[$el['realColName']], 'php:Y-m-d' );
                    } else {
                        $subVal = $this->filters[$el['realColName']];
                    }
                    $this->__endOfPrepare( $subKey, $subVal, $el );
                }
            }
        }
        Yii::trace( \yii\helpers\VarDumper::dumpAsString( $this->_cacheQueryParam ), 'Filters' );
        $this->_filterApplay( $query, $first );
        return $query;
    }

    public function __endOfPrepare( &$subKey, &$subVal, &$el )
    {
        if ( array_key_exists( 'or', $el ) ) {
            $prepare = [
                'where' => ['or', [$subKey => $subVal], $el['or']],
                    //'params'=>[':SUBFTEXT'.$subKeyID++=>$subVal]
            ];
            if ( array_key_exists( 'filterVarName', $el ) ) {
                if ( array_key_exists( 'filterCalculate', $el ) && is_callable( $el['filterCalculate'] ) ) {
                    $prepare['params'][$el['filterVarName']] = call_user_func( $el['filterCalculate'], $subVal );
                } else {
                    $prepare['params'][$el['filterVarName']] = $subVal;
                }
            }
            $this->_cacheQueryParam['where2'][] = $prepare;
        } elseif ( array_key_exists( 'orFilterVar', $el ) ) {
            if ( array_key_exists( 'filterCalculate', $el ) && is_callable( $el['filterCalculate'] ) ) {
                $f = call_user_func( $el['filterCalculate'], $subVal );
            } else {
                $f = $subVal;
            }
            $prepare = [
                'where' => ['or', [$subKey => $subVal], [$el['orFilterVar'] => $f]],
            ];
            $this->_cacheQueryParam['where2'][] = $prepare;
        } elseif(array_key_exists('like',$el)){
            $this->_cacheQueryParam['where2'][] = ['like',$subKey, $subVal.'%',false];
        }else{
            $this->_cacheQueryParam['where'][$subKey] = $subVal;
        }
        if ( array_key_exists( 'andWhere', $el ) && !in_array( $el['andWhere'], $this->_cacheQueryParam['where2'] ) ) {
            $this->_cacheQueryParam['where2'][] = $el['andWhere'];
        }
    }

    protected function checkPechatnikOnStageChange( $id, $stage )
    {
        if ( in_array( (int) $stage, [0, 2, 6, 7, 8, 9] ) ) {
            if ( $pechatnik = Pechiatnik::find()->where( ['z_id' => $id] )->one() ) {
                $pechatnik->delete();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
