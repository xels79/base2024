<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\grid\GridView;
if (!isset($pjaxId)) $pjaxId='ourMangersListPjax';
?>
    <?php    yii\widgets\Pjax::begin([
        'id'=>$pjaxId
    ]);?>
    <?=(Yii::$app->user->identity->can('create')?(Html::tag('div',Html::button('Добавить', ['role'=>'add','class' => 'btn btn-main btn-xs']),['class'=>'dialog-row'])): '')?>
    <?=GridView::widget([
        'dataProvider'=>$dataProvaider,
        'columns'=>[
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'postText:text',
            'name:text',
            'phone1:text',
            'phone2:text',
            'wages:currency',
            'profit:text',
            'superprofit:text',
            'statusText:text'
        ]
    ])?>
    <?php    yii\widgets\Pjax::end()?>