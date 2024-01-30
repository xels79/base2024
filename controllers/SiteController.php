<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use yii\helpers\Html;
use \app\controllers\ControllerTait;
use app\models\zakaz\Zakaz;
use app\models\tables\Worktypes;
use yii\helpers\ArrayHelper;
use app\components\MyHelplers;
use app\models\zakaz\ZakazMaterials;
use app\models\zakaz\ZakazPod;
use app\models\Spendingtoyourselftable;

class SiteController extends ControllerMain {

    use ControllerTait;

    private $exportFile;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'statistic'      => [
                        'get',
                        'post'],
                    'checkmaterials' => ['get'],
                    'logs'           => ['get'],
                    'saveyuospend'=>['post']
                ]
            ]
        ];
    }

    public function actions()
    {
        return [
            'error'          => [
                'class' => 'yii\web\ErrorAction',
            ],
            'statistic'      => 'app\controllers\siteActions\SStatistic',
            'statisticgraph' => 'app\controllers\siteActions\SStatisticGraph',
        ];
    }

    public function beforeAction( $action )
    {
        if ( parent::beforeAction( $action ) ) {
            if ( $action->id === 'error' ) $this->layout = 'main_2.php';
            if ( !$this->logOrErr ) {
                if ( $this->role == 'logist' ) {
                    $this->redirect( [
                        'user/view',
                        'id' => Yii::$app->user->identity->id] );
                    return false;
                } elseif ( ($this->role != 'admin' && $this->role != 'moder') && !$this->logOrErr ) {
                    throw new ForbiddenHttpException( 'Недостаточно прав2' );
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function actionIndex()
    {
        $this->layout = 'main_2.php';
//        $dependency = [
//            'class' => 'yii\caching\DbDependency',
//            'sql' => 'SELECT MAX(zakaz) FROM post',
//        ];
        return $this->render( 'index' );
    }

    public function actionLogin()
    {
        $this->layout = 'login';
        if ( !\Yii::$app->user->isGuest ) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ( $model->load( Yii::$app->request->post() ) && $model->login() ) {
            if ( $user = \app\models\TblUser::findOne( Yii::$app->user->id ) ) {
                Yii::debug( 'user', 'Login - userFound' );
                $user->login_time = time();
                $user->control_time = time();
//                $user->logout_time=0;
                $user->update( [
                    'login_time',
                    'control_time'] );
            }
            if ( \Yii::$app->user->identity->role === 'logist' ) {
                return $this->redirect( [
                            '/zakazi/zakaz/logistlist'] );
            } elseif ( \Yii::$app->user->identity->role === 'bugalter' ) {
                return $this->redirect( [
                            '/zakazi/zakaz/bugalterlist'] );
            } elseif ( \Yii::$app->user->identity->role === 'proizvodstvo' || \Yii::$app->user->identity->role === 'proizvodstvochief' ) {
                return $this->redirect( [
                            '/zakaz/zakazlist/proizvodstvoindex'] );
            } elseif ( \Yii::$app->user->identity->role === 'dizayner' ) {
                return $this->redirect( [
                            '/zakaz/zakazlist/disainerindex'] );
            } else {
                if ( $rUrl = Url::previous() )
                        return $this->redirect( Url::previous() ); //$this->goBack();
                else return $this->goBack(); //$this->goHome();
            }
        } else {
            if ( $oFirm = \app\models\admin\OurFirm::find()->where( 'firm_id > :id', [
                        'id' => 0] )->one() ) {
                $logo = $oFirm->logo;
                $firmName = $oFirm->mainName;
            } else {
                $logo = false;
                $firmName = Yii::$app->name;
            }
            return $this->render( 'login', [
                        'model'    => $model,
                        'logo'     => $logo,
                        'firmName' => $firmName
                    ] );
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect( [
                    'login'] );
    }

    public function actionContact()
    {

        $model = new ContactForm();
        if ( $model->load( Yii::$app->request->post() ) && $model->contact( Yii::$app->params['adminEmail'] ) ) {
            Yii::$app->session->setFlash( 'contactFormSubmitted' );

            return $this->refresh();
        } else {
            return $this->render( 'contact', [
                        'model' => $model,
                    ] );
        }
    }

    public function actionBlocked()
    {
        if ( !\yii::$app->params['isBlocked'] )
                return $this->redirect( $this->brandUrl() );
        $this->layout = 'mainBlocked.php';
        return $this->render( 'blocked' );
    }

    public function actionTestsys()
    {
        $this->layout = 'main_2.php';
        return $this->render( 'testsys' );
    }

    private function export( $txt, $newLine = true )
    {
        fwrite( $this->exportFile, $txt . ($newLine ? "\r\n" : "") );
    }

    private function prepareTableToExport( $tblName )
    {
        $rVal = '';
        $tCreate = \yii::$app->db->createCommand( "show create table $tblName" )->queryAll();
        $rVal .= $tCreate[0]['Create Table'] . ';';
        $dt = \yii::$app->db->createCommand( "SELECT * FROM $tblName" )->queryAll();
        $insertStr = 'INSERT INTO ' . Yii::$app->db->quoteTableName( $tblName ) . ' (';
        $first = true;
        foreach ( $dt as $el ) {
            $values = $first ? '(' : ",(";
            $firstVal = true;
            foreach ( $el as $k => $val ) {
                if ( $first ) {
                    if ( $insertStr[strlen( $insertStr ) - 1] !== '(' )
                            $insertStr .= ',';
                    $insertStr .= Yii::$app->db->quoteColumnName( $k );
                }
                if ( $firstVal ) {
                    $firstVal = false;
                    $values .= ($val !== null ? Yii::$app->db->quoteValue( $val ) : 'NULL');
                } else {
                    $values .= ($val !== null ? ',' . Yii::$app->db->quoteValue( $val ) : ',NULL');
                }
            }
            if ( $first ) {
                $first = false;
                $insertStr .= ') VALUES ';
            }
            $values .= ")";
            $rVal .= $insertStr . $values;
            $insertStr = '';
        }
        return $rVal . ';';
    }

    public function actionTables()
    {
        $this->layout = 'main_2.php';
        return $this->render( 'tables' );
    }

    public function actionExport()
    {
        $fn = realpath( \yii::getAlias( '@app/../../' . \yii::$app->params['sqlBackFolder'] ) );
        if ( $fn ) {
            $fn .= '/' . \yii::$app->params['sqlBackFileName'];
            if ( $this->exportFile = fopen( $fn, 'w' ) ) {
                $this->export( 'set names utf8;' );
                foreach ( \yii::$app->db->schema->tableNames as $tblName )
                    $this->export( $this->prepareTableToExport( $tblName ) );
                fclose( $this->exportFile );
                return Yii::$app->response->sendFile( $fn, 'baseAsterion.sql' );
                //return $this->refresh();
            } else {
                throw new \yii\web\NotFoundHttpException( 'Не возможно создать файл "' . $fn . '"' );
            }
        } else {
            throw new \yii\web\BadRequestHttpException( 'Путь к "' . \yii::getAlias( '@app/../../' . \yii::$app->params['sqlBackFolder'] ) . '" ненайден' );
        }
    }

    public function actionTest()
    {
        $this->layout = 'main_2.php';
        return $this->render( 'test', [
                    'cont' => \yii\helpers\VarDumper::dumpAsString( [], 10, true )] );
    }

    public function actionLogs( $flush = false )
    {
        if ( $flush ) {
            MyHelplers::writeLogs();
            return $this->redirect( ['logs'] );
        }
        $this->layout = 'main_2.php';
        $dt = MyHelplers::readLogs();
        $dataProvider = new \yii\data\ArrayDataProvider( [
            'allModels'  => array_key_exists( 'main', $dt ) ? $dt['main'] : [],
            'pagination' => [
                'pageSize' => 10,
            ],
                ] );
        return $this->render( 'logs', ['dataProvider' => $dataProvider] );
    }

    public function actionCheckmaterials( int $idToErase = 0 )
    {
        $this->layout = 'main_2.php';
        if ( $idToErase ) {
            if ( $model = ZakazMaterials::findOne( $idToErase ) ) {
                $model->delete();
            }
            return $this->redirect( Url::to( ['checkmaterials'] ) );
        }
        $zIds = ArrayHelper::getColumn( Zakaz::find()->select( 'id' )->asArray()->all(), 'id' );
        $lostMaterials = ZakazMaterials::find()
                ->where( ['not', ['zakaz_id' => $zIds]] )
                ->leftJoin( 'firmPost', 'zakaz_materials.firm_Id=firmPost.firm_Id' )
                ->leftJoin( 'materialtypes', 'zakaz_materials.type_id=materialtypes.id' )
                ->select( [
                    'zakaz_materials.id as id',
                    'firmPost.mainName as fName',
                    'materialtypes.name as mName',
                    'zakaz_materials.zakaz_id as zId'
                ] )
                ->asArray()
                ->all();
        //$a1 = Html::tag('p', Html::a('Просмотреть логи', Url::to(['logs'])));
        //$cont = Html::tag('div', \yii\helpers\VarDumper::dumpAsString($lostMaterials, 10, true));
        $dataProvider = new \yii\data\ArrayDataProvider( [
            'allModels'  => $lostMaterials,
            'pagination' => [
                'pageSize' => 10,
            ],
                ] );

        return $this->render( 'lostMaterial', [
                    'dataProvider'      => $dataProvider,
                    'loasMaterialCount' => count( $lostMaterials ),
                    'title'             => 'Потерянные материалы',
                    'infoString'        => 'Количество потерянных материалов:'
                ] );
    }

    public function actionCheckpod( int $idToErase = 0 )
    {
        $this->layout = 'main_2.php';
        if ( $idToErase ) {
            if ( $model = ZakazPod::findOne( $idToErase ) ) {
                $model->delete();
            }
            return $this->redirect( Url::to( ['checkpod'] ) );
        }
        $zIds = ArrayHelper::getColumn( Zakaz::find()->select( 'id' )->asArray()->all(), 'id' );
        $lostMaterials = ZakazPod::find()
                ->where( ['not', ['zakaz_id' => $zIds]] )
                ->leftJoin( 'firmPod', 'zakaz_pod.pod_id=firmPod.firm_Id' )
                ->leftJoin( 'worktypes', 'zakaz_pod.type_id=worktypes.id' )
                ->select( [
                    'zakaz_pod.id as id',
                    'firmPod.mainName as fName',
                    'worktypes.name as mName',
                    'zakaz_pod.zakaz_id as zId'
                ] )
                ->asArray()
                ->all();
        $dataProvider = new \yii\data\ArrayDataProvider( [
            'allModels'  => $lostMaterials,
            'pagination' => [
                'pageSize' => 10,
            ],
                ] );

        return $this->render( 'lostMaterial', [
                    'dataProvider'      => $dataProvider,
                    'loasMaterialCount' => count( $lostMaterials ),
                    'title'             => 'Потерянные материалы',
                    'infoString'        => 'Количество потерянных материалов:'
                ] );
    }   
    public function actionSaveyuospend(){
        Yii::$app->response->format= yii\web\Response::FORMAT_JSON;
        if ($model=Spendingtoyourselftable::find()->where(['id'=>Yii::$app->request->post('id',0)])->one()){
            $model->load(Yii::$app->request->post());
            if ($model->save()){
                return ['status'=>'ok'];
            }else{
                return ['status'=>'error'];
            }
        }else{
            $model=new Spendingtoyourselftable();
            $model->load(Yii::$app->request->post());
            $model->id=(int)Yii::$app->request->post('id',0);
            if ($model->save()){
                return ['status'=>'ok'];
            }else{
                return ['status'=>'error'];
            }
        }
    }
}
