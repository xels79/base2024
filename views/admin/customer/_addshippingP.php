<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use \app\models\admin\RekvizitOur;
?>

<div class="dialog-list form-shipping shipping-pod">
    <?php $form=ActiveForm::begin([
        'id'=>'addShipping',
        'options' => [
            'class' => '',
            'enctype'=>'multipart/form-data'
        ],
        'enableAjaxValidation'=>true,
        'fieldConfig' => [
            'template' => "{label}{input} руб.\n{error}",
        ]
    ]);?>
    <div class="d-first">
        <div>
            <span>Счет</span>
            <?=$form->field($model, 'expense_to')?>
            <?=$form->field($model, 'expense_from')?>
        </div>
        <div>
            <span>Сумма доставки</span>
            <?=$form->field($model, 'summ1')->label(false)?>
            <?=$form->field($model, 'summ2')->label(false)?>
            <?=$form->field($model, 'summ3')?>            
        </div>
        <?php $form->fieldConfig['template']="{label}{input}{error}";?>
        <div>
            <?=$form->field($model, 'is_in_office')->checkbox()?>
            <?=$form->field($model, 'is_in_production')->checkbox()?>
            <?=$form->field($model, 'is_in_shop')->checkbox()?>            
        </div>
    </div>
    <?=$form->field($model, 'firm_id')->hiddenInput()->label(false)?>
    <div class="dialog-row btn-group"><?=Html::submitButton('Сохранить',['class'=>'btn btn-main'])?><?=Html::button('Отмена',['role'=>'cansel','class'=>'btn btn-main-font-black'])?></div>
    <?php if (!$model->isNewRecord) echo $form->field ($model, 'shippingId')->hiddenInput ()->label (false)?>
    <?php ActiveForm::end()?>
</div>