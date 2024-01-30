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

<div class="dialog-list form-shipping">
    <?php
    $form = ActiveForm::begin( [
                'id'                   => 'addShipping',
                'options'              => [
                    'class'   => '',
                    'enctype' => 'multipart/form-data'
                ],
                'enableAjaxValidation' => true,
//        'fieldConfig' => [
//            'options'=>['class'=>'dialog-row']
//        ],
    ] );
    ?>
    <div class="d-first">
        <div>
            <?= $form->field( $model, 'type_id' )->dropDownList( \app\models\admin\ShippingZak::$typeList ) ?>
            <?= $form->field( $model, 'summ1' )->input( 'text', ['autocomplete' => 'off'] ) ?>
<?= $form->field( $model, 'firm_id' )->hiddenInput()->label( false ) ?>
        </div>
        <div>
            <?= $form->field( $model, 'is_transport_company' )->dropDownList( [
                'Нет', 'Да'] ) ?>
<?= $form->field( $model, 'sity' )->input( 'text', ['autocomplete' => 'off'] ) ?>
<?= $form->field( $model, 'summ2' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        </div>
    </div>
    <div class="d-sec">
        <span>Доставка</span>
        <?= $form->field( $model, 'is_to_office' )->dropDownList( ['Нет',
            'Да'] ) ?>
    <?= $form->field( $model, 'is_to_production' )->dropDownList( ['Нет',
        'Да'] ) ?>
    <?= $form->field( $model, 'is_to_shop' )->dropDownList( ['Нет', 'Да'] ) ?>
    </div>
    <div class="dialog-row btn-group"><?= Html::submitButton( 'Сохранить', ['class' => 'btn btn-main'] ) ?><?= Html::button( 'Отмена', [
        'role' => 'cansel', 'class' => 'btn btn-main-font-black'] ) ?></div>
<?php if ( !$model->isNewRecord ) echo $form->field( $model, 'shippingId' )->hiddenInput()->label( false ) ?>
<?php ActiveForm::end() ?>
</div>