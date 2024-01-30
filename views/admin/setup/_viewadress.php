<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\Html;
use \app\models\admin\RekvizitOur;
?>

<div class="dialog-list setup-rekvizit">
    <?=DetailView::widget([
        'model'=>$model,
        'options'=>[
            'class'=>'table table-bordered detail-view'
        ],
        'attributes'=>[
            'placeText:text',
            [
                'attribute'=>'actualAddress',
                'contentOptions'=>['class'=>'dialog-address'],
            ],
            'name:text',
            'phone:text',
        ]
    ])?>
</div>