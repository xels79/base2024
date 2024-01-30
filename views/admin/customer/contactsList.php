<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\grid\GridView;
    if (!isset($perfix))$perfix='Zak';
?>
    <?php    yii\widgets\Pjax::begin([
        'id'=>$perfix.'ContactListPjax'
    ]);?>
    <?=(Yii::$app->user->identity->can('admin/mainpage/ajaxlist_zakone')?(Html::tag('div',Html::button('Добавить', ['role'=>'add','class' => 'btn btn-main btn-xs']),['class'=>'dialog-row'])): '')?>
    <?=GridView::widget([
        'dataProvider'=>$dataProvaider,
        'pager'=>[
            'linkOptions'=>[
                'classN'=>$classN
            ]
        ],
        'columns'=>[
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'postText:text',
            'name:text',
            'phone:text',
            'additional:text',
            'mail:email',
            'statusText:text',
            'comment'
        ]
    ])?>
    <?php    yii\widgets\Pjax::end()?>