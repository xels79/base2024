<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\bootstrap\Html;
use app\models\TblUser;
?>
<div class="accsses-setup">
    <h3 class="text-danger">Изменять с осторожностью!</h3>
    <div class="panel panel-default accsses-setup-menu">
        <div class="panel-heading">Роли</div>
        <div class="panel-body">
            <?=Html::listBox('accsses-setup-menu',null ,TblUser::getUtypesListS(), array_merge(['id'=>'roles-list'],TblUser::getUtypesListSOptions()))?>
        </div>
        <div class="panel-footer"><?= Html::button('Сохранить',['class'=>'btn btn-main btn-xs disabled','id'=>'user-accsses-save'])?></div>
    </div>
    <div class="accsses-setup-content">
        <div class="panel panel-default">
            <div class="panel-heading">Разрешено</div>
            <div class="panel-body"><select id="user-accsses-info-allow" size="4"></select></div>
            <div class="panel-footer"><?= TblUser::getUserAccssesSelectDD('select-allow-actions')?></div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Запрещено</div>
            <div class="panel-body"><select id="user-accsses-info-deny" size="4"></select></div>
            <div class="panel-footer"><?= TblUser::getUserAccssesSelectDD('select-deny-actions')?></div>
        </div>
    </div>
</div>