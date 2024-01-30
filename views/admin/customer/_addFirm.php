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

<div class="dialog-list setup-add-firm">
    <?php
    $form = ActiveForm::begin( [
                'id'                   => 'addFirmForm',
                'options'              => [
                    'class'   => '',
                    'enctype' => 'multipart/form-data'
                ],
                'enableAjaxValidation' => true,
                'fieldConfig'          => [
                    'template'     => "<div class=\"dialog-control-group\">{label}\n{input}</div>\n<span class=\"dialog-error pull-right\">{error}</span>",
                    'labelOptions' => ['class' => 'dialog-col-120'],
                    'inputOptions' => ['class' => 'dialog-col-160'],
                    'options'      => ['class' => 'dialog-row']
                ],
            ] );
    ?>
    <?= $form->field( $model, 'mainForm' )->dropDownList( app\models\admin\Zak::$formList ) ?>
    <?= $form->field( $model, 'mainName' )->input( 'text', ['autocomplete' => 'off'] ) ?>
    <?= $form->field( $model, 'status' )->dropDownList( ['Нет', 'Активен'] ) ?>
    <?= $form->field( $model, 'typeOfPayment' )->dropDownList( \app\models\admin\Zak::$tax_systemList ) ?>
    <?= $model->hasAttribute( 'credit' ) ? $form->field( $model, 'credit' )->input( 'text', [
                'autocomplete' => 'off'] ) : '' ?>
    <?= $model->hasAttribute( 'delay' ) ? $form->field( $model, 'delay' )->input( 'text', [
                'autocomplete' => 'off'] ) : '' ?>
    <?= $form->field( $model, 'has_contract' )->dropDownList( ['Нет', 'Да'] ) ?>
    <?= $form->field( $model, 'contract_number' )->input( 'text', ['autocomplete' => 'off'] ) ?>
    <?php if ( $model->canGetProperty( 'curency_type' ) ): ?>
        <?=
        $form->field( $model, 'curency_type' )->dropDownList( [
            'RUB' => 'Рубли',
            'USD' => 'USD',
            'EUR' => 'EUR'
        ] )
        ?>
    <?php endif; ?>
    <?php if ( $model->canGetProperty( 'blackList' ) && !$model->isNewRecord ): ?>
            <?= $form->field( $model, 'blackList' ) ?>
        <?php endif; ?>
    <div class="dialog-row btn-group"><?= Html::submitButton( 'Сохранить', ['class' => 'btn btn-main'] ) ?><?=
        Html::button( 'Отмена', [
            'role'  => 'cansel', 'class' => 'btn btn-main-font-black'] )
        ?></div>
<?php ActiveForm::end() ?>
</div>
