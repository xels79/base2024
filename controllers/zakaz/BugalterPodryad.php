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
 * Description of BugalterPostav
 *
 * @author xel_s
 */
class BugalterPodryad  extends ZakazListBaseFunction{
    public static $availableColumnsBugalterPodryad=[
        'ourmanagername',
        'podryad_total_coast_list',
        'podryad_name_list',
        'podryad_paied_list',
        'podryad_residue_list'
    ];
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'bugalterpodryadindex'=>['get'],
                    'setsizesmbugalterpodryad'=>['post'],
                    'getavailablecolumnsbugalterpodryad'=>['post'],
                    'setcolumnsbugalterpodryad'=>['post'],
                    'listbugalterpodryad'=>['post'],
                    'paiedbugalterpodryad'=>['post']
                ]
            ]
        ]);
    }
    public function actionBugalterpodryadindex(){
        $this->layout='main_2.php';
        return $this->render('bugalterpodryadindex');
    }
    public function actionGetavailablecolumnsbugalterpodryad(){
        return $this->availabelColumns('materialListBugalterPodryad',self::$availableColumnsBugalterPodryad);
    }
    public function actionSetcolumnsbugalterpodryad(){
        return $this->setcolumns('materialListBugalterPodryad');
    }
    public function actionSetsizesmbugalterpodryad(){
        return $this->setsizesm('materialListBugalterPodryad');
    }
    public function actionPaiedbugalterpodryad(){
        $id=(int) Yii::$app->request->post('id',0);
        $paid=(int) Yii::$app->request->post('paid');
//        return ['status'=>'error','errorText'=>'Ещё не работает'];
        if ($id){
            if ($paid>0){
                if ($model= \app\models\zakaz\ZakazPod::findOne($id)){
                    $model->paid+=$paid;
                    if ($model->save()){
                        return ['status'=>'ok'];
                    }else{
                        return ['status'=>'error','errorText'=>"Ошибка сохранения подрядчика с id($id): ".$model->firstErrors[0]];
                    }
                }else{
                    return ['status'=>'error','errorText'=>"Подрядчика с id($id) не найден"];
                }
            } else {
                return ['status'=>'error','errorText'=>"Неверное значение оплаты ($paid)"];
            }
        }else{
            return ['status'=>'error','errorText'=>'Неверное значение или не передан ID'];
        }
    }
    private function _query(){
        return Zakaz::find()
                ->leftJoin('zakaz_pod','zakaz_pod.zakaz_id=zakaz.id')
                ->where('zakaz_pod.payment>0');
    }
    public function actionListbugalterpodryad(){
        if (($page=(int) Yii::$app->request->post('page',-1))<0) $page=0;
        $rVal=[
            'list'=>[],
            'hidden'=>[],
            'attention'=>[],
            'colOptions'=>$this->getOptions(true,'materialListBugalterPodryad'),
        ];
        $rVal['pageSize']=(int) (isset($rVal['colOptions']['pageSize'])?$rVal['colOptions']['pageSize']:10);
        $colsN= $this->getCollName($rVal['colOptions']);
        $queryPrepary=$this->_query();
        $this->filterApplay($queryPrepary,false,true);
        $rVal['count']=$queryPrepary->count();
        $material_total_coast=$queryPrepary->sum('zakaz_pod.payment');
        $material_total_paied=$queryPrepary->sum('zakaz_pod.paid');
        $rVal['hidden2']=[
            'podryad_total_coast_list'=>Yii::$app->formatter->asInteger($material_total_coast),
            'podryad_paied_list'=>Yii::$app->formatter->asInteger($material_total_paied),
            'podryad_residue_list'=>Yii::$app->formatter->asInteger($material_total_coast-$material_total_paied),
        ];
        $pageCnt=(int)ceil($rVal['count']/$rVal['colOptions']['pageSize']);
        if ($page>=$pageCnt) $page=$pageCnt-1;
        $rVal['page']=$page;
        $queryPrepary= $this->_query()
                ->offset($page*$rVal['colOptions']['pageSize'])
                ->limit($rVal['colOptions']['pageSize']);
        $this->filterApplay($queryPrepary,false,true);
        $rVal['tmpSort']=$this->orderSting;
        $queryPrepary->orderBy($this->orderSting);
        $query=$queryPrepary->all();
        if ($query){
            $diffCol=array_diff($colsN,$query[0]->attributes());
            $opt=$rVal['colOptions'];
            foreach ($query as $el){
                $tmpRv=[];
                foreach ($colsN as $key) $tmpRv[$key]=$el[$key];
                $rVal['list'][]=$tmpRv;
                $rVal['hidden'][$el['id']]=[
                    'stage'=>$el['stage'],
                    'podryad_total_coast_list'=>$el['podryad_total_coast_list'],
                    'podryad_paied_list'=>$el['podryad_paied_list'],
//                    'material_residue_list'=>$el['material_residue_list'],
                ];
            }
        }
        $rVal['pCnt']=$pageCnt;
        $rVal['colN']=$colsN;
        $rVal['sortable']=['dateofadmissionText'];
        $rVal['filters']=$this->_filters;
//        $rVal['post']=Yii::$app->request->post();
        return $rVal;
        
    }
}
