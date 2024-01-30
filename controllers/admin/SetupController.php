<?php

namespace app\controllers\admin;

use yii;
use app\controllers\AjaxController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\TblUserSearch;
use app\models\admin\OurFirm;
use app\models\admin\RekvizitOur;
use app\models\admin\AddressOur;
use yii\data\ActiveDataProvider;

class SetupController extends AjaxController {

    public $defaultAction = 'list';

    public function init()
    {
        parent::init();
        $this->viewPath = '@app/views/admin/setup';
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'index'            => [
                        'get',
                        'post'],
                    'ajaxlist_ourfirm' => [
                        'post'],
                    'userchange'       => [
                        'post'],
                    'userremove'       => [
                        'post'],
                    'saveaccssesrul'   => [
                        'post']
                ],
            ],
        ];
    }

    /**
     * Lists all Firms models.
     * @return mixed
     */
    public function beforeAction( $action )
    {
        if ( parent::beforeAction( $action ) ) {
            $this->view->params['assetBundle'] = '\app\assets\AppAssetUsers';
            if ( $action->id === 'index' ) {
//                $this->createBreadcrumbs($action);
            }
            return true;
        } else {
            return false;
        }
    }

    public function actionIndex()
    {
        return $this->render( 'index', [
                ] );
    }

    public function actionAjaxlist_ourfirm( $req = null, $page = 1 )
    {
        if ( !$req ) $req = Yii::$app->request->post( 'req', 1 );
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ( $req && isset( $req['name'] ) ) {
            if ( $req['name'] == 'addreqvizit' ) return $this->_addSF( $req );
            if ( $req['name'] == 'validatereqvizit' )
                    return $this->_validateSF( $req, Yii::$app->request->post( 'id', 0 ) );
            if ( $req['name'] == 'viewreqvizit' )
                    return $this->_viewSF( $req, Yii::$app->request->post( 'id' ) );
            if ( $req['name'] == 'changereqvizit' )
                    return $this->_changeSF( $req, Yii::$app->request->post( 'id' ) );
            if ( $req['name'] == 'removereqvizit' )
                    return $this->_removeSF( $req, Yii::$app->request->post( 'id' ) );
            if ( $req['name'] == 'addaddress' )
                    return $this->_addSF( $req, 'AddressOur', '_addouraddress' );
            if ( $req['name'] == 'changeaddress' )
                    return $this->_changeSF( $req, Yii::$app->request->post( 'id' ), 'AddressOur', '_addouraddress' );
            if ( $req['name'] == 'viewaddress' )
                    return $this->_viewSF( $req, Yii::$app->request->post( 'id' ), 'AddressOur', '_viewadress' );
            if ( $req['name'] == 'removeaddress' )
                    return $this->_removeSF( $req, Yii::$app->request->post( 'id' ), 'AddressOur' );
            if ( $req['name'] == 'addmaneger' )
                    return $this->_addSF( $req, 'ManagersOur', '_addourmanager' );
            if ( $req['name'] == 'changemaneger' )
                    return $this->_changeSF( $req, Yii::$app->request->post( 'id' ), 'ManagersOur', '_addourmanager' );
            if ( $req['name'] == 'viewmaneger' )
                    return $this->_viewSF( $req, Yii::$app->request->post( 'id' ), 'ManagersOur', '_viewourmaneger' );
            if ( $req['name'] == 'removemaneger' )
                    return $this->_removeSF( $req, Yii::$app->request->post( 'id' ), 'ManagersOur' );
            if ( $req['name'] == 'changeourfirm' )
                    return $this->_changeSF( $req, Yii::$app->request->post( 'id' ), 'OurFirm', '_piclogoourfirm' );
        }
        $firm = OurFirm::find()->where( 'firm_id is not null' )->one();
        if ( !$firm ) {
            $firm = new OurFirm();
            if ( $firm->load( Yii::$app->request->post(), 'OurFirm' ) ) {
                if ( $firm->validate() ) {
                    if ( !$firm->save() ) {
                        return [
                            'status'    => 'error',
                            'errors'    => $firm->errors,
                            'errorText' => 'Ошибка валидации',
                            'modelName' => $firm->formName(),
                            'files'     => $_FILES,
                            'post'      => $_POST];
                    }
                } else
                        return [
                        'status'    => 'error',
                        'errors'    => $firm->errors,
                        'errorText' => 'Ошибка валидации',
                        'modelName' => $firm->formName(),
                        'files'     => $_FILES,
                        'post'      => $_POST];
            } else {
                return [
                    'html'   => $this->renderPartial( 'addourfirm', [
                        'model' => $firm] ),
                    'status' => 'form2',
                    'files'  => $_FILES,
                    'post'   => $_POST];
            }
        }
        $provaiderRekvizit = new ActiveDataProvider( [
            'query'      => RekvizitOur::find()->where( [
                'firm_id' => $firm->firm_id] ),
            'pagination' => [
                'pageSize' => 20,
                'page'     => $page - 1
            ]
                ] );
        $provaiderAdresses = new ActiveDataProvider( [
            'query'      => AddressOur::find()->where( [
                'firm_id' => $firm->firm_id] ),
            'pagination' => [
                'pageSize' => 20,
                'page'     => $page - 1
            ]
                ] );
        $provaiderManagers = new ActiveDataProvider( [
            'query'      => \app\models\admin\ManagersOur::find()
                    ->where( [
                        'firm_id' => $firm->firm_id] )
                    ->andWhere( [
                        'post' => 4] ),
            'pagination' => [
                'pageSize' => 20,
                'page'     => $page - 1
            ]
                ] );
        $provaiderEmployee = new ActiveDataProvider( [
            'query'      => \app\models\admin\ManagersOur::find()
                    ->where( [
                        'firm_id' => $firm->firm_id] )
                    ->andWhere( [
                        'not',
                        [
                            'post' => 4]] ),
            'pagination' => [
                'pageSize' => 20,
                'page'     => $page - 1
            ]
                ] );
        return [
            'html'   => $this->renderPartial( 'firmourList', [
                'model'             => $firm,
                'provaiderRekvizit' => $provaiderRekvizit,
                'provaiderAdresses' => $provaiderAdresses,
                'provaiderManagers' => $provaiderManagers,
                'provaiderEmployee' => $provaiderEmployee
            ] ),
            'status' => 'ok',
            'files'  => $_FILES,
            'post'   => $_POST];
    }

    public function actionAjaxlist_user()
    {
//        if (!$rq=Yii::$app->request->post('req'))
//                if ($rq=Yii::$app->request->get('req'))
//                    unset($_GET['req']);
//        else
//            unset($_POST['req']);
//
        $searchModel = new TblUserSearch();
        $dataProvider = $searchModel->search( Yii::$app->request->queryParams );
        $dataProvider->pagination->pageSize = 8;
        return $this->renderPartial( 'userList', [
                    'searchModel'  => $searchModel,
                    'dataProvider' => $dataProvider,
                ] );
    }

    public function actonAjaxchange()
    {
        return $this->render( 'index', [
                ] );
    }

    private function addRekvizit()
    {
        $rVal = [
            'status' => 'ok'];
        return $rVal;
    }

    public function actionAjaxadd()
    {
//        $act=Yii::$app->request->post(act);
//        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//        switch($act){
//            case 'RekvizitOur':
//                return $this->addRekvizit();
//            default:
//                return ['status'=>'error','errorText'=>'Не задано действие $act'];
//        }
//        return $this->render('index', [
//        ]);
    }

    public function actionAjaxremove()
    {
        return $this->render( 'index', [
                ] );
    }

    public function actionSaveaccssesrul()
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $rulls = Yii::$app->request->post( 'rulls' );
        if ( !$rulls || !is_array( $rulls ) )
                return [
                'status'    => 'error',
                'errorText' => 'Не переданны данные'];
        $errors = [
        ];
        foreach ( $rulls as $key => $val ) {
            if ( $model = \app\models\MyRbac::findOne( (int) $key ) ) {
                $model->value = $val;
                if ( !$model->save() ) {
                    $errors[(int) $key] = $model->errors;
                }
            }
        }
        if ( count( $errors ) ) {
            return [
                'status' => 'error',
                'errors' => $errors];
        } else {
            return [
                'status' => 'ok'];
        }
    }

    public function actionUserchange()
    {
        $id = (int) Yii::$app->request->post( 'id', 0 );
        if ( $id ) {
            $model = \app\models\TblUser::findOne( (int) $id );
        } else {
            $model = new \app\models\TblUser();
        }
        if ( $model ) {
            $utypeBack = $model->utype;
            if ( $model->load( Yii::$app->request->post() ) ) {
                Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                if ( Yii::$app->user->identity->role !== 'admin' )
                        $model->utype = $utypeBack;
                if ( $model->save() ) {
                    return [
                        'status' => 'ok'];
                } else {
                    return [
                        'status' => 'error',
                        'errors' => $model->errors];
                }
            } else
                    return $this->renderPartial( 'userChange', [
                            'model' => $model] );
        } else {
            return "<p>Заказ <em>$id</em> не найден</p>";
        }
    }

    public function actionUserremove()
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        if ( $model = \app\models\TblUser::findOne( (int) Yii::$app->request->post( 'id', 0 ) ) ) {
            $model->delete();
            return [
                'status'  => 'ok',
                'message' => 'Пользователь удален!'];
        } else {
            return [
                'status'    => 'error',
                'errorText' => 'Запись с id #' . (int) Yii::$app->request->post( 'id', 0 ) . ' не найдена!'];
        }
    }

}
