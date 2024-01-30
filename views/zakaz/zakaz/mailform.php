<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 *  @var $this yii\web\View
 *  @var $model app\models\Mail
 *  @var $index int
 *  $var $firmKey int
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="send-file">
    <?php $form = ActiveForm::begin([
        'action'=>Url::to(['/zakaz/zakaz/mailfile', 'id'=>$id, 'idZipResorch'=>$idZipResorch, 'tmpName'=>$tmpName]),
        'options' => [
            'class' => 'form-horizontal col-lg-11',
            'enctype' => 'multipart/form-data',
            'id'=>'file-send-form',
            'autocomplete'=>'off'
        ],
        'layout'=>'horizontal',
        'fieldConfig' => [
             'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-4',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]);
    ?>
    <?= $form->field($model, 'toEmail')->textInput(['placeholder'=>'На пример: post@yandex.ru','autocomplete'=>'fake-name-disable-autofill']) ?>
    <?= $form->field($model, 'subject')->textInput(['placeholder'=>'Укажите тему']) ?>
    <?= $form->field($model, 'fromName')->textInput(['placeholder'=>'Представьтесь']) ?>
    <?= $form->field($model, 'fromEmail')->textInput(['placeholder'=>'На пример: post@yandex.ru']) ?>
    <?= $form->field($model, 'mailBody')->textarea(['placeholder'=>'Текст сообщения', 'rows'=>5]) ?>
    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
        <?= Html::button('Отменить', ['id'=>'file_send_cansel', 'class' => 'btn btn-default'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>