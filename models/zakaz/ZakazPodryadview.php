<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazPodryadview
 *
 * @author Александр
 */
use app\models\zakaz\ZakazPod;
use yii\widgets\DetailView;
use yii\helpers\Html;
abstract class ZakazPodryadview extends ZakazSpecification{
    public function getPodryadview(){
        $tmp=ZakazPod::find()->where(['zakaz_id'=>$this->id])
                ->leftJoin('worktypes','worktypes.id=workType')
                ->leftJoin('contactPod','contactPod.contactPod_id=manager_id')
                ->leftJoin('firmPod','firmPod.firm_id=contactPod.firm_id')
                ->select([
                    'worktypes.name as workTName',
                    'firmPod.mainName as firmName',
                    'coast'
                ])
                ->asArray()
                ->all();
        $rVal='';$pos=0;
        $opt=[
            ['attribute'=>'firmName','label'=>'Фирма:','format'=>'text'],
            ['attribute'=>'workTName','label'=>'Вид работы:','format'=>'text'],
            ['attribute'=>'coast','label'=>'Сумма:','format'=>'decimal']            
        ];
        foreach($tmp as $el){
            if (!$pos)
                $rVal.='<tr>';
            $rVal.='<td>'.DetailView::Widget([
                'model'=>$el,
                'attributes'=>$opt,
                'options'=>['class' => 'table table-striped table-bordered detail-view sub'],
            ]).'</td>';
            if ($pos){
                $pos=0;
                $rVal.='</tr>';
            }else{
                $pos++;
            }
        }
        if ($pos) $rVal.='<td></td></tr>';
        return Html::tag('table',$rVal,['class'=>'table']);
    }
}
