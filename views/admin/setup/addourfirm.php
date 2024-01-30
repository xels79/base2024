<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\widgets\ActiveForm;
?>
<div class="dialog-list">
    <?php $form=ActiveForm::begin([
        'id'=>'addOurFirm',
        'options' => ['class' => 'form-horizontal form-add'],
        'enableAjaxValidation'=>true,
        'fieldConfig' => [
            'template' => "<div class=\"col-lg-2\">{label}</div>\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]);?>
    <h4>Наша фирма не создана заполните поля:</h4>
    <?=$form->field($model, 'mainName')?>
    <?php ActiveForm::end();?>
</div>