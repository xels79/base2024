<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="dialog-list">
    <?php $form=ActiveForm::begin([
        'id'=>'ourFirmPics',
        'options'=>[
            'enctype'=>"multipart/form-data"
        ]
    ]);?>
        <div class="dialog-row">
        <div class="dialog-col-240 field-manager-foto t-cell">
            <?=$model->renderFileInput('logo',[
                'label'=>FALSE,
                'inputOptions'=>[
                    'role'=>'openImgRekvizit',
                    'class'=>'btn btn-main',
                    'content'=>$model->attributeLabels()['logo']
                ],
                'hiddenInputName'=>'logoFile'
            ],'visible',true,true)?>
        </div>                
        <div class="dialog-col-240 field-manager-foto t-cell">
            <?=$model->renderFileInput('pic1',[
                'label'=>FALSE,
                'inputOptions'=>[
                    'role'=>'openImgRekvizit',
                    'class'=>'btn btn-main',
                    'content'=>$model->attributeLabels()['pic1']
                ],
                'hiddenInputName'=>'pic1File'
            ],'visible',true,true)?>
        </div>                
        <div class="dialog-col-240 field-manager-foto t-cell">
            <?=$model->renderFileInput('pic2',[
                'label'=>FALSE,
                'inputOptions'=>[
                    'role'=>'openImgRekvizit',
                    'class'=>'btn btn-main',
                    'content'=>$model->attributeLabels()['pic2']
                ],
                'hiddenInputName'=>'pic2File'
            ],'visible',true,true)?>
        </div>                
    </div>
    <div class="dialog-row">
        <?=Html::submitButton('Сохранить',['class'=>'btn btn-main'])?>
    </div>
    <?php ActiveForm::end()?>
        
</div>