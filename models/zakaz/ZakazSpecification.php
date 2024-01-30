<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazSpecification
 *
 * @author Александр
 */
use Yii;
use yii\helpers\Json;
use app\components\MyHelplers;
use app\models\zakaz\ZakazMaterials;
use yii\widgets\DetailView;
use yii\helpers\Html;
abstract class ZakazSpecification extends ZakazConstants{
    protected $_materialAll=null;
    abstract public function getMaterials();
    protected function detViewAttr($label,$value,$format='text'){
        return ['label'=>$label,'value'=>$value,'format'=>$format];
    }
    
    private static $_getMaterialAllSQLCache=[];
    private static $_getMaterialAllSQLCache2=[];
    protected function getMaterialAll($supplierType=-1,$isNullDeliver=false){
        if ($this->_materialAll)
            return $this->_materialAll;
        else{
            $queryopt=['zakaz_id'=>$this->id];
            if ($supplierType>-1) $queryopt['supplierType']=2;
            if ($isNullDeliver) $queryopt['delivery_date']=null;
            
            //Кэш
            if ($supplierType>-1 && !$isNullDeliver){
                if (array_key_exists($this->id, self::$_getMaterialAllSQLCache)){
                    $tmp=self::$_getMaterialAllSQLCache[$this->id];
                }else{
                    $tmp=ZakazMaterials::find()->where($queryopt)->all();
                    self::$_getMaterialAllSQLCache[$this->id]=$tmp;
                }
            }else
                $tmp=ZakazMaterials::find()->where($queryopt)->all();
            $this->_materialAll=[];
            Yii::debug($tmp,'getMaterialAll');
            foreach ($tmp as $el){
                
                //Кэш
                if (array_key_exists($el->type_id, self::$_getMaterialAllSQLCache2)){
                    $mat=self::$_getMaterialAllSQLCache2[$el->type_id];
                }else{
                    $mat= \app\models\tables\Tablestypes::findOne($el->type_id);
                    self::$_getMaterialAllSQLCache2[$el->type_id]=$mat;
                }
                if ($mat){
                    $tmpData=$el->toArray();
                    $tMat=MyHelplers::materialInfoByContent($mat,$tmpData);
                    Yii::debug($tMat,'test material #id:'.$this->id);
                    $this->_materialAll[]=[
                        'name'=>$tMat['value']['name'],
                        'tranlitName'=>$tMat['value']->translitName,
                        'supplierTypeText'=>$el->supplierTypeText,
                        'firm_id'=>$el->firm_id,
                        'postav'=>$tMat['postav'],
                        'struct'=> array_combine(Json::decode($tMat['value']->struct),array_reverse($tMat['query'])),
                        'count'=>$el->count,
                        'coast'=>$el->coast,
                        'order_date'=>$el->order_date,
                        'delivery_date'=>$el->delivery_date,
                        'structBase'=>Json::decode($tMat['value']->struct),
                        'id'=>$el['id'],
                        'paid'=>$el['paid'],
                        'supplierType'=>$el['supplierType']
                        
                    ];
                }else{
                    $this->_materialAll[]=[
                        'name'=>'Не найден',
                        'supplierTypeText'=>'',
                        'postav'=>'',
                        'struct'=>[],
                        'count'=>0,
                        'coast'=>0,
                        'order_date'=>$el->order_date,
                        'delivery_date'=>$el->delivery_date,
                        'id'=>0
                    ];
                }
            }
            return $this->_materialAll;
        }
    }
    public function getSpecification(){
        $outBuff='';
        $n=0;
        foreach ($this->getMaterialAll() as $val){
            $opt=[
                $this->detViewAttr('Матер.:', $val['name']),
                $this->detViewAttr('Чей матер:', $val['supplierTypeText']),
                $this->detViewAttr('Поставщик:', $val['postav']),
            ];
            foreach ($val['struct'] as $key=>$val) $opt[]=$this->detViewAttr($key,$val);
            if (!$n++){
                $outBuff.='<tr><td>'.DetailView::widget([
                'model'=>[1],
                'attributes'=>$opt,
                'options'=>['class' => 'table table-striped table-bordered detail-view sub']
                ]).'</td>';
            }else{
                $n=0;
                $outBuff.='<td>'.DetailView::widget([
                'model'=>[1],
                'attributes'=>$opt,
                'options'=>['class' => 'table table-striped table-bordered detail-view sub']
                ]).'</td></tr>';
            }
        }
        if ($n) $outBuff.='<td></td></tr>';
        return Html::tag('table',$outBuff,['class'=>'table']);
    }
}
