<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
//use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\TblUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tbl-user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 128]) ?>

    <?php if ($model->isNewRecord){
                echo $form->field($model, 'npassword',['options'=>['class'=>  'require']])->passwordInput(['maxlength' => 128]);
          }else{
                echo $form->field($model, 'npassword')->passwordInput(['maxlength' => 128])->hiddenInput()->label(Button::widget([
                    'label' => 'Изменить пароль',
                    'options' => ['class' => 'btn-sm btn-warning','type'=>'button'],
                    'id'=>'tglPassword'
                    //'href'=>'#'
                ]));
                //echo $form->field($model, 'password')->passwordInput(['maxlength' => 128])->hiddenInput()->label(false);
          }
    ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 128]) ?>

    <?php 
        if (Yii::$app->user->identity->role=='admin')
            echo  $form->field($model, 'utype')->dropDownList([
                1=>'Администратор',
                2=>'Модератор',
                3=>'Логистика',
                4=>'Бугалтер',
                5=>'Дизайнер',
                6=>'Производство',
            ])->label ('Уровень доступа');
    ?>

    <?= $form->field($model, 'realname')->textInput(['maxlength' => 128]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
        $this->registerJs('$("#tglPassword").on("click",function(e){'
                //. '$("#tglPassword").css({display:"none"});'
                .'$("#tglPassword").parent().parent().children("input").attr("type","password");'
                .'$("#tglPassword").parent().html("Новый пароль");'
                . '});', yii\web\View::POS_END, 'showPasswordJS');
    ?>
</div>
