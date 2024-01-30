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

/**
 * Description of ZakazBugalter
 *
 * @author Александр
 */
class ZakazBugalter  extends ZakazProduct{
    public static $availableColumnsBugalter=[
        'deadlineText',
        'dateofadmissionText',
        'ourmanagername',
        'name',
        'zak_idText',
        'production_idText',
        'total_coastText',
        'material_total_coast',
        'calulateProfit',
        'podryad_total_coast'
    ];
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'bugalterindex'=>['get'],
                    'setsizesmbugalter'=>['post'],
                    'getavailablecolumnsbugalter'=>['post'],
                    'setcolumnsbugalter'=>['post'],
                    'listbugalter'=>['post'],
                    'stagebugalter'=>['post']
                ]
            ]
        ]);
    }
    public function actionBugalterindex(){
        $this->layout='main_2.php';
        return $this->render('bugalterindex');
    }
    public function actionGetavailablecolumnsbugalter(){
        return $this->availabelColumns('materialListBugalter',self::$availableColumnsBugalter);
    }
    public function actionSetcolumnsbugalter(){
        return $this->setcolumns('materialListBugalter');
    }
    public function actionSetsizesmbugalter(){
        return $this->setsizesm('materialListBugalter');
    }
    public function actionStagebugalter(){
        $id=(int) Yii::$app->request->post('id',0);
        $stage=(int) Yii::$app->request->post('stage',-1);
        if ($id && $stage>-1 && ($stage===3 || $stage===4 || $stage===6 || $stage===8)){
            if ($model=Zakaz::findOne($id)){
                $model->stage=$stage;
                if ($model->update(false,['stage'])){
                    if ($this->checkPechatnikOnStageChange($id,(int)$stage)){
                        return ['status'=>'ok','pechatnikTable'=>'hasChange'];
                    }else{
                        return ['status'=>'ok'];
                    }
                } else {
                    return [
                        'status'=>'error',
                        'errorText'=>"Заказ $id не удалось сохранить",
                        'errors'=>$model->errors
                    ];
                }
            }else
                return ['status'=>'error','errorText'=>"Заказ $id не найден"];
        }else{
            return ['status'=>'error','errorText'=>'Неверное значение ID или STAGE'];
        }
    }
    public function actionListbugalter(){
        if (($page=(int) Yii::$app->request->post('page',0))<0) $page=0;
        $rVal=[
            'list'=>[],
            'hidden'=>[],
            'attention'=>[],
            'colOptions'=>$this->getOptions(true,'materialListBugalter'),
        ];
        $rVal['pageSize']=(int) (isset($rVal['colOptions']['pageSize'])?$rVal['colOptions']['pageSize']:10);
        $colsN= $this->getCollName($rVal['colOptions']);
        $queryPrepary=Zakaz::find();//->where(['=','stage',4])->orWhere(['=','stage',5])->orWhere(['=','stage',6]);
        $this->filterApplay($queryPrepary,false,true);
        $rVal['count']=$queryPrepary->count();
        $pageCnt=(int)ceil($rVal['count']/$rVal['colOptions']['pageSize']);
        if ($page>=$pageCnt) $page=$pageCnt-1;
        $rVal['page']=$page;
        $queryPrepary= Zakaz::find()//->where(['=','stage',4])->orWhere(['=','stage',5])->orWhere(['=','stage',6])
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
                $rVal['hidden'][$el['id']]=['stage'=>$el['stage']];
            }
        }
        $rVal['pCnt']=$pageCnt;
        $rVal['colN']=$colsN;
        $rVal['sortable']=['dateofadmissionText'];
        $rVal['filters']=[];
        $rVal['stageLevels']=Zakaz::$_stage;
        $material_total_coast= \app\models\zakaz\ZakazMaterials::find()->sum('count*coast');
        $rVal['hidden2']=['material_total_coast'=>$material_total_coast];
        return $rVal;
        
    }
}
