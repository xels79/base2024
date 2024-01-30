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

<div class="dialog-list setup-rekvizit">
    <?php $form=ActiveForm::begin([
        'id'=>'addOurFirm',
        'options' => [
            'class' => '',
            'enctype'=>'multipart/form-data'
        ],
        'enableAjaxValidation'=>true,
        'fieldConfig' => [
            'template' => "<div class=\"dialog-control-group\">{label}\n{input}</div>\n<span class=\"dialog-error pull-right\">{error}</span>",
            'labelOptions' => ['class' => 'dialog-col-160'],
            'inputOptions' => ['class' => 'dialog-col-200'],
            'options'=>['class'=>'dialog-row']
        ],
    ]);?>
    <div class="dialog-col-50">
        <?=$form->field($model,'form')->dropDownList(app\models\admin\RekvizitOur::$formList)?>
        <?=$form->field($model, 'name')?>
        <?=$form->field($model, 'inn')?>
        <?=$form->field($model, 'kpp')?>
        <?=$form->field($model, 'address')->textarea(['rows'=>2])?>
        <?=$form->field($model, 'consignee')?>
        <?=$form->field($model, 'account')?>
        <?=$form->field($model, 'bik')->textInput(['role'=>'banksearch'])?>
        <?=$form->field($model, 'bank')->textarea(['rows'=>2])?>
        <?=$form->field($model, 'correspondentAccount')?>
        <?=$form->field($model, 'ogrn')?>
        <?=$form->field($model, 'okpo')?>
        <?=$form->field($model, 'okved')?>
        <?=$form->field($model, 'firm_id')->hiddenInput()->label(false)?>
    </div>
    <div class="dialog-col-50">
        <?=$form->field($model, 'ceo')?>
        <div class="dialog-row field-rekvizitour-signatureCEO">
            <?=$model->renderFileInput('signatureCEO',[
                'label'=>FALSE,
                'inputOptions'=>[
                    'role'=>'openImgRekvizit',
                    'class'=>'btn btn-main',
                    'content'=>$model->attributeLabels()['signatureCEO']
                ],
                'hiddenInputName'=>'imgSignatureCEO'
            ])?>
        </div>
        <?=$form->field($model, 'nameChiefAccountant')?>
        <div class="dialog-row field-rekvizitour-signatureCEO">
            <?=$model->renderFileInput('signatureAccountant',[
                'label'=>FALSE,
                'inputOptions'=>[
                    'role'=>'openImgRekvizit',
                    'class'=>'btn btn-main',
                    'content'=>$model->attributeLabels()['signatureAccountant']
                ],
                'hiddenInputName'=>'imgSignatureAccountant'
            ])?>
        </div>
        <div class="dialog-row field-rekvizitour-stamp">
            <?=$model->renderFileInput('stamp',[
                'label'=>FALSE,
                'inputOptions'=>[
                    'role'=>'openImgRekvizit',
                    'class'=>'btn btn-main',
                    'content'=>$model->attributeLabels()['stamp']
                ],
                'hiddenInputName'=>'imgStamp'
            ])?>
        </div>
    </div>
    <div class="dialog-row" style="border-top:1px solid;">
        <?=$form->field($model, 'tax_system_id',['labelOptions'=>['class'=>'dialog-col-240']])->dropDownList(RekvizitOur::$tax_systemList)?>
        <?=$form->field($model, 'tax_id',['labelOptions'=>['class'=>'dialog-col-240']])->dropDownList(RekvizitOur::$taxList)?>
    </div>
    <div class="dialog-row btn-group"><?=Html::submitButton('Сохранить',['class'=>'btn btn-main'])?><?=Html::button('Отмена',['role'=>'cansel','class'=>'btn btn-main-font-black'])?></div>
    <?php if (!$model->isNewRecord) echo $form->field ($model, 'rekvizit_id')->hiddenInput ()->label (false)?>
    <?php ActiveForm::end()?>
</div>