<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Html;
?>

<?php    Pjax::begin([
    'id'=>'shippingListPjax'
]);?>
<?=(Yii::$app->user->identity->can('admin/mainpage/ajaxlist_zakone')?(Html::tag('div',Html::button('Добавить', ['role'=>'add','class' => 'btn btn-main btn-xs']),['class'=>'dialog-row'])): '')?>
<?= ListView::widget([
    'itemView'=>'_shippingListP',
    'dataProvider'=>$dataProvaider,
    'pager'=>[
        'linkOptions'=>[
            'classN'=>$classN
        ]
    ],

//    'itemOptions'=>['data-message'=>'']
])?>
<?php Pjax::end();?>