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
class BugalterPostav  extends BugalterPodryad{
    public static $availableColumnsBugalterPost=[
        'ourmanagername',
        'material_total_coast_list',
        'material_post_list',
        'material_paied_list',
        'material_residue_list'
    ];
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'bugalterpostindex'=>['get'],
                    'setsizesmbugalterpost'=>['post'],
                    'getavailablecolumnsbugalterpost'=>['post'],
                    'setcolumnsbugalterpost'=>['post'],
                    'listbugalterpost'=>['post'],
                    'paiedbugalterpost'=>['post']
                ]
            ]
        ]);
    }
    public function actionBugalterpostindex(){
        $this->layout='main_2.php';
        return $this->render('bugalterpostindex');
    }
    public function actionGetavailablecolumnsbugalterpost(){
        return $this->availabelColumns('materialListBugalterPost',self::$availableColumnsBugalterPost);
    }
    public function actionSetcolumnsbugalterpost(){
        return $this->setcolumns('materialListBugalterPost');
    }
    public function actionSetsizesmbugalterpost(){
        return $this->setsizesm('materialListBugalterPost');
    }
    public function actionPaiedbugalterpost(){
        $id=(int) Yii::$app->request->post('id',0);
        $paid=(int) Yii::$app->request->post('paid');
        if ($id){
            if ($paid>0){
                if ($model= \app\models\zakaz\ZakazMaterials::findOne($id)){
                    $model->paid+=$paid;
                    if ($model->save()){
                        return ['status'=>'ok'];
                    }else{
                        return ['status'=>'error','errorText'=>"Ошибка сохранения материала с id($id): ".$model->firstErrors[0]];
                    }
                }else{
                    return ['status'=>'error','errorText'=>"Материал с id($id) не найден"];
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
                ->leftJoin('zakaz_materials','zakaz_materials.zakaz_id=zakaz.id')
                ->where('zakaz_materials.count*zakaz_materials.coast>0');
    }
    public function actionListbugalterpost(){
        if (($page=(int) Yii::$app->request->post('page',-1))<0) $page=0;
        $rVal=[
            'list'=>[],
            'hidden'=>[],
            'attention'=>[],
            'colOptions'=>$this->getOptions(true,'materialListBugalterPost'),
        ];
        $rVal['pageSize']=(int) (isset($rVal['colOptions']['pageSize'])?$rVal['colOptions']['pageSize']:10);
        $colsN= $this->getCollName($rVal['colOptions']);
        $queryPrepary=$this->_query();
        $this->filterApplay($queryPrepary,false,true);
        $rVal['count']=$queryPrepary->count();
        $material_total_coast=$queryPrepary->sum('zakaz_materials.count*zakaz_materials.coast');
        $material_total_paied=$queryPrepary->sum('zakaz_materials.paid');
        $rVal['hidden2']=[
            'material_total_coast_list'=>Yii::$app->formatter->asInteger($material_total_coast),
            'material_paied_list'=>Yii::$app->formatter->asInteger($material_total_paied),
            'material_residue_list'=>Yii::$app->formatter->asInteger($material_total_coast-$material_total_paied),
        ];
        $pageCnt=(int)ceil($rVal['count']/$rVal['colOptions']['pageSize']);
        if (!$page && ($_page!==0 && $_page!=='0')) $page=$pageCnt;
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
                    'material_total_coast_list'=>$el['material_total_coast_list'],
                    'material_paied_list'=>$el['material_paied_list'],
                    'material_residue_list'=>$el['material_residue_list'],
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
