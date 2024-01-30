<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$ddOpt = [
];
if (isset($otherRequestParam['manager'])) {
    $model->post = 4;
} else {
    $ddOpt['options'] = [
        4 => [
            'readonly' => true]
    ];
}
?>
<div class="dialog-list setup-manager">
    <?php
    $form = ActiveForm::begin([
                'id'                   => 'addOurManager',
                'options'              => [
                    'class'   => '',
                    'enctype' => 'multipart/form-data'
                ],
                'enableAjaxValidation' => true,
                'fieldConfig'          => [
                    'template'     => "<div class=\"dialog-control-group\">{label}\n{input}</div>\n<span class=\"dialog-error pull-right\">{error}</span>",
                    'labelOptions' => [
                        'class' => 'dialog-col-hf'],
                    'inputOptions' => [
                        'class' => 'dialog-col-hf'],
                    'options'      => [
                        'class' => 'dialog-row']
                ],
    ]);
    ?>
    <table class="table">
        <tr>
            <td width="50%">
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'phone1') ?>
                <?= $form->field($model, 'phone2') ?>
                <?=
                $form->field($model, 'address')->textarea([
                    'rows' => 2])
                ?>
                <?= $form->field($model, 'inn') ?>
                <?= $form->field($model, 'snils') ?>
                <?= $form->field($model, 'firm_id')->hiddenInput()->label(false) ?>
            </td>
            <td width="50%">
                <div class="dialog-row field-manager-foto">
                    <?=
                    $model->renderFileInput('foto', [
                        'label'           => FALSE,
                        'inputOptions'    => [
                            'role'    => 'openImgRekvizit',
                            'class'   => 'btn btn-main',
                            'content' => $model->attributeLabels()['foto']
                        ],
                        'hiddenInputName' => 'fotoFile'
                            ], 'visible')
                    ?>
                </div>
            </td>
        </tr>

        <tr>
            <td width="50%">
                <?= $form->field($model, 'passport_series') ?>
                <?= $form->field($model, 'passport_number') ?>
                <?= $form->field($model, 'passport_given') ?>
                <?= $form->field($model, 'passport_given_date', ['inputOptions' => ['value' => $model->passport_given_date !== null ? Yii::$app->formatter->asDate($model->passport_given_date) : '']]) ?>
            </td>
            <td width="50%">
                <?= $form->field($model, 'birthday') ?>
            </td>
        </tr>
        <tr><td colspan="2"><?=
                $form->field($model, 'registration', [
                    'inputOptions' => [
                        'class' => ''],
                    'options'      => [
                        'class' => 'dialog-100perc']])
                ?></td></tr>
        <tr><td colspan="2" width="100%">
                <table class="table">
                    <tr>
                        <td>
                            <?php
                            if (isset($otherRequestParam['manager'])) {
                                echo $form->field($model, 'postText')->textInput([
                                    'readonly' => true]);
                                echo $form->field($model, 'post')->hiddenInput()->label(false);
                            } else
                                echo $form->field($model, 'post')->dropDownList(\app\models\admin\ManagersOur::$post_list, $ddOpt);
                            ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'payment_id', ['options' => ['title' => app\models\admin\ManagersOur::$payment_list_title[$model->payment_id]]])->dropDownList(\app\models\admin\ManagersOur::$payment_list) ?>
                        </td>
                        <td><?=
                            $form->field($model, 'hasPercents')->dropDownList([
                                'Нет',
                                'Да'])
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?=
                            $form->field($model, 'employed')->dropDownList([
                                'Нет',
                                'Да'])
                            ?>
                        </td>
                        <td><?= $form->field($model, 'wages', ['inputOptions' => ['value' => $model->wages !== null ? round($model->wages) : 0]]) ?></td>
                        <td><?= $form->field($model, 'ourfirm_profit', ['inputOptions' => ['readonly' => !$model->hasPercents]]) ?></td>
                    </tr>
                    <tr>
                        <td>
                            <?=
                            $form->field($model, 'status_id')->dropDownList(\app\models\admin\ManagersOur::$status_list)
                            ?>
                        </td>
                        <td><?= $form->field($model, 'normal', ['inputOptions' => ['value' => $model->normal ? round($model->normal) : 0]]) ?></td>
                        <td><?= $form->field($model, 'profit', ['inputOptions' => ['readonly' => !$model->hasPercents]]) ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?= $form->field($model, 'recycling_rate') ?></td>
                        <td><?= $form->field($model, 'superprofit', ['inputOptions' => ['readonly' => !$model->hasPercents]]) ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?= $form->field($model, 'piecework') ?></td>
                        <td><?= $form->field($model, 'material_profit', ['inputOptions' => ['readonly' => !$model->hasPercents]]) ?></td>
                    </tr>
                </table>
            </td></tr>
    </table>
    <div class="dialog-row btn-group"><?=
        Html::submitButton('Сохранить', [
            'class' => 'btn btn-main'])
        ?><?=
        Html::button('Отмена', [
            'role'  => 'cansel',
            'class' => 'btn btn-main-font-black'])
        ?></div>
    <?php if (!$model->isNewRecord) echo $form->field($model, 'managerOur_id')->hiddenInput()->label(false) ?>
    <?php ActiveForm::end() ?>
</div>