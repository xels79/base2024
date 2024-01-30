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
        'id'=>'ourFirmListPjax'
    ]);?>
    <?=(Yii::$app->user->identity->can('create')?(Html::tag('div',Html::button('Добавить', ['id'=>'ourRekvizitAdd','class' => 'btn btn-main btn-xs','role'=>'add']),['class'=>'dialog-row'])): '')?>
    <?=GridView::widget([
        'dataProvider'=>$dataProvaider,
        'columns'=>[
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            [
                'label'=>'Юридическая фирма',
                'content'=>function ($model){
                    return $model::$formList[$model->form].' "'.$model->name.'"';
                }
            ],
            'inn:text',
            'bik:text',
            'account',
            'active:boolean',
        ]
    ])?>
    <?=Html::hiddenInput('current_page',$dataProvaider->pagination->page)?>
    <?php    yii\widgets\Pjax::end()?>