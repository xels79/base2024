<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of MaterialController
 *
 * @author Александр
 */
use Yii;
use app\controllers\ControllerMain;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\MyHelplers;
use yii\filters\VerbFilter;
use app\models\tables\Tablestypes;
use app\models\tables\DependTables;
use \app\models\tables\DependTable;
use app\models\tables\MaterialsOnFirms;
//use app\models\zakaz\ZakazMaterials;
use \yii\helpers\Html;
use yii\helpers\Json;

class MselectController extends ControllerMain {
    use ControllerTait;
    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list' => ['post'],
                ],
            ],
        ];
    }

    private function proceedSearch(){
        $rVal=[];
        $q=MaterialsOnFirms::find();
        $q->rightJoin('firmPost','materials_on_firms.firm_id = firmPost.firm_id');
        $q->rightJoin('materialtypes','materials_on_firms.m_type = materialtypes.id');
        $q->select([
            'materials_on_firms.*',
            'firmPost.mainName',
            'materialtypes.name as materialName',
            'materialtypes.translitName as materialTranslitName',
            'materialtypes.struct as materialStruct',
        ]);
        $q->orderBy('firmPost.mainName,materialName');
        $tmp=$q->asArray()->all();
        $cnt=count($tmp);
        
        //Yii::trace($tmp,'MSelect');
        $base_struct = DependTables::structBas();
        for ($i=0; $i<$cnt;$i++){
            if (!$tmp[$i]['id']) continue;
            $_rusNames=Json::decode($tmp[$i]['materialStruct']);
            $_dTablesNames=[];
            $_tStruct=[];
            foreach ($_rusNames as $el){
                if (array_key_exists($el, $base_struct)){
                    $_dTablesNames[]= [
                        'name'=>$base_struct[$el]['tblNamePart'],
                        'type'=>$base_struct[$el]['type']
                    ];
                    $_tStruct[]=DependTables::creatFullTableName($tmp[$i]['materialTranslitName'], $base_struct[$el]['tblNamePart']);
                }
            }
            $dependValues=[[]];
            if (count($_tStruct)>1){
                $n=count($_tStruct)-1;
                $dtQ= DependTable::createObject($_tStruct[count($_tStruct)-1]);
                $dtQ=$dtQ::find()->where(['`'.$_tStruct[$n].'`.`id`'=>$tmp[$i]['m_id']]);
                $dtQSelect=['`'.$_tStruct[$n].'`.`name` as '.$_dTablesNames[$n]['name']];
                $n--;
                for (;$n>-1;$n--){
                    $dtQ->leftJoin($_tStruct[$n], '`'.$_tStruct[$n].'`.`id`=`'.$_tStruct[$n+1].'`.`reference_id`');
                    $dtQSelect[]= '`'.$_tStruct[$n].'`.`name` as '.$_dTablesNames[$n]['name']; 
                }
                $dtQ->select($dtQSelect);
                $dependValues=$dtQ->asArray()->all();
                //Yii::trace($dependValues, 'MSelect');
            }
            $tmp[$i]['dependValues']=$dependValues[0];
            $rVal[]=[
                'id'=>$tmp[$i]['id'],
                'money'=>[
                    'coast'=>$tmp[$i]['coast'],
                    'optcoast'=>$tmp[$i]['optcoast'],
                    'optfrom'=>$tmp[$i]['optfrom']
                ],
                'mainParams'=>[
                    'firmName'=>$tmp[$i]['mainName'],
                    'materialName'=>$tmp[$i]['materialName']
                ],
                'dependValues'=>$dependValues[0],
                'depandTableNames'=>$_dTablesNames
            ];
        }
        return $rVal;
    }
    
    public function actionList() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $oldUpdateTime=(int)Yii::$app->request->post('oldUpdateTime',0);
        $cache=Yii::$app->cache;
        $data = $cache->get('materials_quick_search');
        $maxU= MaterialsOnFirms::find()->max('`update`');
        if (!$data || (int)$data['maxU']<$maxU){
            $data=[
                'answer'=>$this->proceedSearch(),
                'maxU'=>$maxU
            ];
        }
        $cache->set('materials_quick_search', $data, 3600);
        if ($oldUpdateTime<$data['maxU']){
            return [
                'status'=>'update',
                'answer'=>$data['answer'],
                'updateTime'=>$maxU,
                '$oldUpdateTime'=>$oldUpdateTime
            ];
        }else{
            return ['status'=>'ok'];
        }
    }

}
