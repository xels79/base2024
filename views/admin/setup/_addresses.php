<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\grid\GridView;
?>
    <?php    yii\widgets\Pjax::begin([
        'id'=>'ourAddressListPjax'
    ]);?>
    <?=(Yii::$app->user->identity->can('create')?(Html::tag('div',Html::button('Добавить', ['role'=>'add','class' => 'btn btn-main btn-xs']),['class'=>'dialog-row'])): '')?>
    <?=GridView::widget([
        'dataProvider'=>$dataProvaider,
        'columns'=>[
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'placeText:text',
            'actualAddress:text',
            'name:text',
            'phone:text',
        ]
    ])?>
    <?php    yii\widgets\Pjax::end()?>