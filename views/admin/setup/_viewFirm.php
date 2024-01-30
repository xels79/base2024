<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @property yii\db\ActiveRecord $model
 */

use yii\widgets\DetailView;
$attr=[
    [
        'attribute'=>'mainForm',
        'value'=>function ($model){
            return $model::$formList[$model->mainForm];
        },
        'format'=>'text'
    ],
    'mainName:text:Название',
    [
        'attribute'=>'status',
        'value'=>function ($model){
            return $model->status?'Активен':'нет';
        },
        'format'=>'text'
    ],
    [
        'attribute'=>'typeOfPayment',
        'value'=>function ($model){
            return $model::$tax_systemList[$model->typeOfPayment];
        },
        'format'=>'text'
    ],
    'contract_number'
];
    if ($model->formName()==='Post'||$model->formName()==='Pod'){
        $attr[]='wopInfoString:text';
    }
?>

<div class="dialog-list view-address">
    <?=DetailView::widget([
        'model'=>$model,
        'options'=>[
            'class'=>'table table-bordered detail-view'
        ],
        'attributes'=>$attr
    ])?>
</div>