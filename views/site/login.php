<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = $this->context->title . '/Вход';
?>
<div class="center-block">
    <div><p><?= Yii::$app->params['aplicationName'] ?> v<?= Yii::$app->version ?><small><i><?= $firmName ?></i></small></p></div>
    <div <?= $logo && file_exists( $logo ) ? 'class="with-logo"' : '' ?>>
        <?php
        if ( $logo && file_exists( $logo ) ) {
            echo Html::tag( 'div', Html::img( $this->assetManager->publish( $logo )[1] ), [
                'class' => 'lin-title-first'] );
        }
        ?>
        <div class="lin-title"><?= Html::encode( 'Вход' ) ?></div>
    </div>
    <?php
    $form = ActiveForm::begin( [
                'id'          => 'login-form',
                'options'     => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template'     => "<div class=\"col-xs-4 col-md-4 col-lg-4\">{label}</div>\n<div class=\"col-xs-8 col-md-8 col-lg-8\">{input}</div>\n<div class=\"col-xs-14\">{error}</div>",
                    'labelOptions' => ['class' => 'control-label'],
                    'options'      => ['class' => 'row']
                ],
            ] );
    ?>

    <?= $form->field( $model, 'username' ) ?>

    <?= $form->field( $model, 'password' )->passwordInput() ?>

    <?= ''/* $form->field($model, 'rememberMe', [
      'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
      ])->checkbox() */ ?>

    <div class="row">
        <div class="col-lg-offset-1 col-lg-11">
            <?=
            Html::submitButton( 'Войти', ['class' => 'btn btn-primary btn-sm',
                'name'  => 'login-button'] )
            ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>

</div>