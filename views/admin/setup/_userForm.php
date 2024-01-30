<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\models\admin\OurFirm;
$firm=OurFirm::find()->one();

$form = ActiveForm::begin([
    'id' => 'user-form',
    'layout' => 'horizontal',
    'fieldConfig'=>[
            'horizontalCssClasses' => [
            'wrapper' => 'col-sm-6',
            'label'=>'col-sm-4'
        ]
    ]
]) ?>

<?= $form->field($model, 'id',['inputOptions'=>['readonly'=>true,'type'=>'hidden']])->label(false)?>
<?= $form->field($model, 'realname')->label('Реальн. имя') ?>
<?= $form->field($model, 'username')->label('Имя пользов.') ?>
<?= $form->field($model, 'email') ?>
<?php if (Yii::$app->user->identity->role==='admin'):?>
    <?= $form->field($model, 'utype')->dropDownList($model->utypesList)?>
    <?= $form->field($model, 'wages') ?>
    <?= $form->field($model, 'percent1')->label($firm?('% '.$firm->mainName):('% '.Yii::$app->params['id'])) ?>
    <?= $form->field($model, 'percent2') ?>
    <?= $form->field($model, 'percent3') ?>

<?php endif;?>
<?php if (!$model->isNewRecord):?>
<?= $form->field($model, 'passwordnew')->hiddenInput()->label('')?>
<div class="form-group">
    <div class="btn-group">
        <?= Html::button('Изменить пароль',['class'=>'btn btn-warning','id'=>'user-change-pass'])?>
    </div>
</div>
<?php else:?>
<?= $form->field($model, 'passwordnew')->passwordInput()->label('Пароль')?>
<?php endif;?>

<?php ActiveForm::end() ?>