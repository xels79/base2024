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

<div class="dialog-list firm-rekvizit">
    <?php
    $form = ActiveForm::begin( [
                'id'                   => 'addRekvizit',
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
    <div class="dialog-col-50">
        <?= $form->field( $model, 'form' )->dropDownList( app\models\admin\RekvizitOur::$formList ) ?>
        <?= $form->field( $model, 'name' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'inn' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'kpp' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'address' )->textarea( ['rows' => 2] ) ?>
        <?= $form->field( $model, 'consignee' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'account' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'bik' )->input( 'text', ['autocomplete' => 'off'] )->textInput( [
            'role' => 'banksearch'] ) ?>
        <?= $form->field( $model, 'bank' )->textarea( ['rows' => 2] ) ?>
        <?= $form->field( $model, 'correspondentAccount' )->input( 'text', [
            'autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'ogrn' )->input( 'text', ['autocomplete' => 'off'] ) ?>
<?= $form->field( $model, 'okpo' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'okved' )->input( 'text', ['autocomplete' => 'off'] ) ?>
        <?= $form->field( $model, 'firm_id' )->hiddenInput()->label( false ) ?>
    </div>
    <div class="dialog-col-50">
                <?= $form->field( $model, 'ceo', ['options' => ['id' => 'rekvizit-ceo']] ) ?>
        <div class="passport-info"<?= !$model->form ? ' style="opacity: 0.2;"' : '' ?>>
            <div class="dialog-row">
                <label class="dialog-col-160">Пасспорт</label>
                <?=
                $form->field( $model, 'passportSeries', [
                    'inputOptions' => ['disabled' => !$model->form]
                ] )
                ?>
                <?=
                $form->field( $model, 'passportNumber', [
                    'inputOptions' => ['disabled' => !$model->form]
                ] )
                ?>
<?=
$form->field( $model, 'passportGiven', [
    'inputOptions' => ['disabled' => !$model->form]
] )
?>
                <?=
                $form->field( $model, 'passportGivenDate', [
                    'inputOptions' => ['disabled' => !$model->form],
                    'options'      => ['id' => 'rekvizit-passportgivendate']
                ] )->textInput( ['value' => $model->passportGivenDate ? Yii::$app->formatter->asDate( $model->passportGivenDate ) : ''] )
                ?>
            </div>
            <div class="dialog-row">
                <label class="dialog-col-160">Свидетельство</label>
                <?=
                $form->field( $model, 'certificateSeries', [
                    'inputOptions' => ['disabled' => !$model->form]
                ] )
                ?>
    <?=
    $form->field( $model, 'certificateNumber', [
        'inputOptions' => ['disabled' => !$model->form]
    ] )
    ?>
<?=
$form->field( $model, 'certificateGiven', [
    'inputOptions' => ['disabled' => !$model->form]
] )
?>
<?=
$form->field( $model, 'certificateGivenDate', [
    'inputOptions' => ['disabled' => !$model->form],
    'options'      => ['id' => 'rekvizit-certificategivendate']
] )
?>
            </div>
        </div>
    </div>
    <div class="dialog-row btn-group"><?= Html::submitButton( 'Сохранить', ['class' => 'btn btn-main'] ) ?><?= Html::button( 'Отмена', [
    'role' => 'cansel', 'class' => 'btn btn-main-font-black'] ) ?></div>
<?php if ( !$model->isNewRecord ) echo $form->field( $model, 'rekvizit_id' )->hiddenInput()->label( false ) ?>
<?php ActiveForm::end() ?>
</div>