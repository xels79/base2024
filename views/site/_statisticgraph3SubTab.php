<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\grid\GridView;
use app\components\TotalColumn;
use yii\widgets\Pjax;
use yii\helpers\Html;

Pjax::begin([
        'options'=>['role'=>'month-filter-pjax','data-reinit-target'=>$chkBoxName],
        'formSelector'=>'#'.$formId,
        'timeout'=>7000
    ]);
    echo Html::beginForm('','post',[
        'id' => $formId,
        'data' => ['pjax' => true],
        'class'=>'form-horizontal gr-form',
        'style'=>'margin-top:10px;',
        'role'=>'form-month-filter'
    ]);
?>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <input name="<?=$chkBoxName?>" type="hidden" value="<?=$$chkBoxName==='on'?'on':'off'?>">
        <button type="submit" class="btn btn-<?=$$chkBoxName==='on'?'danger':'default'?>">Показать только этот месяц</button>
    </div>
</div>
<?php
    Pjax::begin([
        'id'=>$pjaxId,
        'timeout'=>7000
    ]);
        echo GridView::widget([
            'dataProvider' => $dataProvider,//$crossGraph[0]['dataProvider'],
            'showFooter' => true,
            'columns' => [
                ['class' => yii\grid\SerialColumn::className()],
                'zakazData:date:Дата заказа',
                'date:date:Дата оплаты',
                'zakaz_id:text:Заказ №',
                [
                    'class'=>TotalColumn::class,
                    'total'=>$total,//$crossGraph[0]['total'],
                    'attribute'=>'summOplata',
                    'format'=>'currency',
                    'label'=>'Оплата'
                ],
                [
                    'class'=>TotalColumn::class,
                    'total'=>$total,//$crossGraph[0]['total'],
                    'attribute'=>'profit',
                    'format'=>'currency',
                    'label'=>'Прибыль с данной оплаты'
                ],
            ],
            ]);
    Pjax::end();
    echo Html::endForm();
Pjax::end();
