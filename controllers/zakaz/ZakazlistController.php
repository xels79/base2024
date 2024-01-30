<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz;
use Yii;
use yii\filters\VerbFilter;
use app\components\MyHelplers;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use app\models\Options;
use \app\models\zakaz\Zakaz;
use yii\helpers\ArrayHelper;
use app\models\zakaz\ZakazMaterials;
use app\controllers\zakaz\ZakazListBaseFunction;
/**
 * Description of ZakazlistController
 *
 * @author Александр
 */
class ZakazlistController extends ZakazProduct{
    public $formClassName='app\models\zakaz\Zakaz';
    public $cacheName='zakazlist';
    public $defaultAction = 'index';
    public function init() {
        parent::init();
        self::$availableColumns=[
            'dateofadmissionText',
            'deadlineText',
            'ourmanagername',
            'total_coastText',
            'name',
            'number_of_copiesText',
            'number_of_copies1Text',
            'production_idText',
            'zak_idText',
            'stageText',
            'method_of_payment_text',
            'oplata_status_text',
            'calulateProfitText'
        ];     
    }
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                    'index'=>['get'],
                    'list'=>['post'],
                    'setsizesm'=>['post'],
                    'getavailablecolumnsmaterials'=>['post'],
                    'setcolumnsm'=>['post'],
                    'getoneraw'=>['post'],
                    'changerow'=>['post'],
                    'view'=>['get','post'],
                    'materialindex'=>['get'],
                    'listmaterial'=>['post'],
                    'changematerialstate'=>['post']
                ],
                
            ],
        ]);
    }
    public function actionView($asDialog=false,$backPage=0,$id=null,$technicals=false,$technicalsPrin=false,$isDisainer=false){
        if (is_string($technicalsPrin)) $technicalsPrin=$technicalsPrin==='true'?true:false;
        if (is_string($asDialog)) $asDialog=$asDialog==='true'?true:false;
        if (is_string($technicals)) $technicals=$technicals==='true'?true:false;
        if ($id!==null){
            if ($model= Zakaz::findOne((int)$id)){
                $opt=['id'=>$id,'asDialog'=>$asDialog,'backPage'=>$backPage,'model'=>$model,'technicalsPrin'=>$technicalsPrin||$isDisainer!==false];
                if ($technicals&&$technicalsPrin){
                    return $this->render('technicals',$opt);
                }else{
                    if ($asDialog){
                        return $this->renderPartial($technicals?'technicals':'view',$opt);
                    }else{
                        $this->layout='main_2.php';
                        return $this->render($technicals?'technicals':'view',$opt);
                    }
                }
            }else{
                return "Заказ №$id не найден";
            }
        }else{
            throw new \yii\web\BadRequestHttpException("Ошибочный параметры");
        }
    }
    public function actionIndex(){
        $showReprint=Yii::$app->request->get('showReprint')==='true';
        $this->layout='main_2.php';
        return $this->render('index',['showReprint'=>$showReprint]);
    }
    public function actionMaterialindex(){
//        $isProduct=Yii::$app->request->get('isproduct',false);
        $this->layout='main_2.php';
        return $this->render('materialindex',['isProduct'=>$this->isproduct]);
    }
    public function actionChangerow(){
        $id=(int) Yii::$app->request->post('id',0);
        $row=Yii::$app->request->post('row');
        if ($id&&$row&& is_array($row)){
            if ($model= Zakaz::findOne($id)){
                foreach ($row as $key=>$val){
                    if ($key==='stage'){
                        $model->setAttribute ($key, (int)$val);
                        $this->checkPechatnikOnStageChange($id,(int)$val);
                    }else
                        $model->setAttribute ($key, (int)$val);
                        //$model[$key]=$val;
                }
                if ($model->update(false, array_keys($row))){
                    return $this->getOneRow($id);
                }else{
                    return [
                        'status'=>'error',
                        'errorText'=>"Ошибка сохранения заказа №$id",
                        'errors'=>$model->errors,
                        'row'=>$row,
                        'post'=>Yii::$app->request->post(),
                        'lastZakaz'=>Yii::$app->user->identity->lastZakaz
                    ];
                }
            }else{
                return ['status'=>'error','errorText'=>"Заказ №$id - не найден"];
            }
        }else{
            return ['status'=>'error','errorText'=>'Неверные параметры'];
        }
    }
    protected function findInSArray($key,&$arr){
        if ($pos=$this->findPosInSArray($key, $arr)!==false){
            return $arr[$pos];
        }else{
            return null;
        }
    }
    public function actionSetsizesm(){
        return $this->setsizesm('materialList');
    }
    public function actionGetavailablecolumnsmaterials(){
        return $this->availabelColumns('materialList',self::$availableColumnsMaterial);
    }
    public function actionSetcolumnsm(){
        return $this->setcolumns('materialList');
    }
    private function findZakaz(int $num){
        $rVal=[
            'list'=>[],
            'hidden'=>[],
            'attention'=>[],
            'colOptions'=>$this->getOptions(),
            'lastZakaz'=>Yii::$app->user->identity->lastZakaz,
        ];
        $rVal['pageSize']=(int) (isset($rVal['colOptions']['pageSize'])?$rVal['colOptions']['pageSize']:10);
        $colsN= $this->getCollName($rVal['colOptions']);
        $counts=Zakaz::find()->select([
            'sum(1) as total',
            'sum(if (zakaz.id<'.$num.',1,0)) as countBefore',
            'sum(if (zakaz.id='.$num.',1,0)) as isFound'
        ])->asArray()->all();
        
        $rVal['$counts']=$counts[0];
        $rVal['count']=(int)$counts[0]['total'];
        $page=$counts[0]['isFound']?floor($counts[0]['countBefore']/$rVal['pageSize']):0;
        if (!$counts[0]['isFound']){
            $rVal['inform']=[
                'headerText'=>'Внимание',
                'text'=>'Заказ №'.$num.' - не найден'
            ];
        }
        $rVal['$page']=$page;
        $pageCnt=(int)ceil($rVal['count']/$rVal['colOptions']['pageSize']);
        if ($page>=$pageCnt) $page=$pageCnt-1;
        $rVal['page']=$page;
        $queryTemp= Zakaz::find()
                ->offset($page*$rVal['colOptions']['pageSize'])
                ->limit($rVal['colOptions']['pageSize'])
                ->orderBy($this->orderSting);
        $query=$queryTemp->all();
        if ($query){
            $diffCol=array_diff($colsN,$query[0]->attributes());
            $opt=$rVal['colOptions'];
            foreach ($query as $el){
                $tmpRv=[];
                foreach ($colsN as $key) $tmpRv[$key]=$el[$key];
                if ($el->attention&& mb_strlen($el->attention)){
                    $rVal['attention'][(int)$el->id]=$el->attention;
                }
                $rVal['list'][]=$tmpRv;
                $rVal['hidden'][$el['id']]=[
                    'stage'=>$el['stage'],
                    'ourmanager_id'=>$el['ourmanager_id'],
                    'is_express'=>$el['is_express'],
                    'fileCount'=>$el->fileCount,
                    'is_express'=>$el->is_express,
                    're_print'=>$el->re_print
                ];
            }
        }
        $rVal['pCnt']=$pageCnt;
        $rVal['colN']=$colsN;
        $rVal['sortable']=['id','dateofadmissionText','deadlineText'];
        $rVal['filters']=[];
        $rVal['post']=Yii::$app->request->post();
        $rVal['stageLevels']=Zakaz::$_stage;
        //$rVal['sort']=Yii::$app->request->post('sort',[]);
        return $rVal;

    }
    public function actionList(){
        if (Yii::$app->request->post('findZakaz')) return $this->findZakaz((int)Yii::$app->request->post('findZakaz'));
        $_page=Yii::$app->request->post('page');
        if (($page=(int) $_page)<0) $page=0;
        $filters=Yii::$app->request->post('filters',null);
        if (!is_array($filters)) $filters=null;
        $rVal=[
            'list'=>[],
            'hidden'=>[],
            'attention'=>[],
            'colOptions'=>$this->getOptions(),
            'lastZakaz'=>Yii::$app->user->identity->lastZakaz
        ];
        $showReprint=Yii::$app->request->post('showReprint')==='true';
        $rVal['pageSize']=(int) (isset($rVal['colOptions']['pageSize'])?$rVal['colOptions']['pageSize']:10);
        $colsN= $this->getCollName($rVal['colOptions']);
        $queryTemp=Zakaz::find();
        $this->filterApplay($queryTemp);
        if ($showReprint){
            $queryTemp->andWhere(['not',['re_print'=>0]]);
        }
        $rVal['count']=$queryTemp->count();
        $pageCnt=(int)ceil($rVal['count']/$rVal['colOptions']['pageSize']);
        if (!$page && ($_page!==0 && $_page!=='0')) $page=$pageCnt;
        if ($page>=$pageCnt) $page=$pageCnt-1;
        $rVal['page']=$page;
        $queryTemp= Zakaz::find()
                ->offset($page*$rVal['colOptions']['pageSize'])
                ->limit($rVal['colOptions']['pageSize'])
                ->orderBy($this->getOrderSting());
                //->orderBy($this->getOrderSting(!Yii::$app->request->post( 'sort')?['id'=>'DESC']:[]));
        $this->filterApplay($queryTemp);
        if ($showReprint){
            $queryTemp->andWhere(['not',['re_print'=>0]]);
        }
        $query=$queryTemp->all();
        if ($query){
            $diffCol=array_diff($colsN,$query[0]->attributes());
            $opt=$rVal['colOptions'];
            foreach ($query as $el){
                $tmpRv=[];
                foreach ($colsN as $key) $tmpRv[$key]=$el[$key];
                if ($el->attention&& mb_strlen($el->attention)){
                    $rVal['attention'][(int)$el->id]=$el->attention;
                }
                $rVal['list'][]=$tmpRv;
                $rVal['hidden'][$el['id']]=[
                    'stage'=>$el['stage'],
                    'ourmanager_id'=>$el['ourmanager_id'],
                    'is_express'=>$el['is_express'],
                    'fileCount'=>$el->fileCount,
                    'is_express'=>$el->is_express,
                    're_print'=>$el->re_print
                ];
            }
        }
        $rVal['pCnt']=$pageCnt;
        $rVal['colN']=$colsN;
        $rVal['sortable']=['id','dateofadmissionText','deadlineText'];
        $rVal['filters']=$this->_filters;
        $rVal['post']=Yii::$app->request->post();
        $rVal['stageLevels']=Zakaz::$_stage;
        //$rVal['sort']=Yii::$app->request->post('sort',[]);
        return $rVal;
    }
    private function getOneRow($id){
        $opt=$this->getOptions();
        $colsN= $this->getCollName($opt);
        if ($query=Zakaz::find()->where(['id'=>$id])->one()){
            $diffCol=array_diff($colsN,$query->attributes());
            $tmpRv=[];
            foreach ($colsN as $key) $tmpRv[$key]=$query[$key];
            return ['status'=>'ok','attention'=>$query->attention,'row'=>$tmpRv,
                'lastZakaz'=>Yii::$app->user->identity->lastZakaz,
                'hidden'=>[
                'stage'=>$query['stage'],
                'ourmanager_id'=>$query['ourmanager_id'],
                'is_express'=>$query['is_express'],
                'fileCount'=>$query->fileCount,
                'is_express'=>$query->is_express,
                're_print'=>$query->re_print,
            ]];
        }else{
            return ['status'=>'error','errorText'=>'Заказ №'.$id.' не найден'];
        }
    }
    public function actionGetoneraw(){
        $opt=$this->getOptions();
        if ($id=Yii::$app->request->post('id')){
            return $this->getOneRow($id);
        }else{
            return ['status'=>'error','errorText'=>'Не передан id заказа'];
        }
    }
    public function actionChangematerialstate(){
        $key='order_date';$key2='material_ordered';
        $val=Yii::$app->request->post($key2);
        if (!$val){
            $key='delivery_date';$key2='material_delivery';
            $val=Yii::$app->request->post($key2);
        }
        if ($val){
            if ($id=Yii::$app->request->post('id')){
                if ($mat= ZakazMaterials::findOne((int)$id)){
                    $mat[$key]=Yii::$app->formatter->asDate($val,'php:Y-m-d');
                    if ($mat->save()){
                        return ['status'=>'ok','post'=>$_POST];
                    }else{
                        return ['status'=>'error','errorText'=>'Материал с id:"'.$id.'" ошибка сохранения','errors'=>$mat->errors];
                    }
                }else{
                    return ['status'=>'error','errorText'=>'Материал с id:"'.$id.'" не найден','val'=>$val,'key'=>$key,'post'=>$_POST];;
                }
            }else{
                return ['status'=>'error','errorText'=>'Не передан id материала','val'=>$val,'key'=>$key,'post'=>$_POST];
            }
        }else{
            return ['status'=>'error','errorText'=>'Не переданно значение','val'=>$val,'key'=>$key,'post'=>$_POST];
        }
    }
}
