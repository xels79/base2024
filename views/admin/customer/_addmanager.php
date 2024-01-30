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
    <?php
    $form = ActiveForm::begin( [
                'id'                   => 'addContact',
                'options'              => [
                    'class'   => '',
                    'enctype' => 'multipart/form-data'
                ],
                'enableAjaxValidation' => true,
                'fieldConfig'          => [
                    'template'     => "<div class=\"dialog-control-group\">{label}\n{input}</div>\n<span class=\"dialog-error pull-right\">{error}</span>",
                    'labelOptions' => ['class' => 'dialog-col-160'],
                    'inputOptions' => ['class' => 'dialog-col-200'],
                    'options'      => ['class' => 'dialog-row']
                ],
    ] );
    ?>
    <?= $form->field( $model, 'name' )->input( 'text', ['autocomplete' => 'off'] ) ?>
    <?= $form->field( $model, 'phone' )->input( 'text', ['autocomplete' => 'off'] ) ?>
    <?= $form->field( $model, 'mail' )->input( 'text', ['autocomplete' => 'off'] ) ?>
    <?= $form->field( $model, 'grant' )->dropDownList( ['Нет', 'Да'] ) ?>
    <?= $form->field( $model, 'firm_id' )->hiddenInput()->label( false ) ?>
    <div class="dialog-row btn-group"><?= Html::submitButton( 'Сохранить', ['class' => 'btn btn-main'] ) ?><?= Html::button( 'Отмена', [
        'role' => 'cansel', 'class' => 'btn btn-main-font-black'] ) ?></div>
<?php if ( !$model->isNewRecord ) echo $form->field( $model, array_keys( $model->getPrimaryKey( true ) )[0] )->hiddenInput()->label( false ) ?>
<?php ActiveForm::end() ?>
</div>