<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\bootstrap\Tabs;
?>
<div class="dialog-list">
    <?= Tabs::widget([
        'id'=>'user-list-tab',
        'items'=>[
            [
                'label'=>'Пользователи',
                'content'=>(Yii::$app->user->identity->can('create')?Html::a('Добавить', '#', ['class' => 'btn btn-main btn-xs','id'=>'user-add']): ''). $this->render('_userlist', ['dataProvider'=>$dataProvider])
            ],
            [
                'label'=>'Настройка уровней доступа',
                'content'=>$this->render('_accssesSetup', [])
            ]
        ]
    ])?>
</div>

