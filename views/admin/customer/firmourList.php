<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*
 *  @property yii\db\ActiveRecord $model
 */

use yii\helpers\Html;
use yii\bootstrap\Tabs;

$items = [
    [
        'label'   => 'Контакты',
        'content' => $this->render('contactsList', [
            'dataProvaider' => $provaiderContacts,
            'firmId'        => $model->firm_id,
            'classN'        => $model->formName()]),
    ],
    [
        'label'   => 'Адреса',
        'content' => $this->render('addressesList', [
            'dataProvaider' => $provaiderAdresses,
            'firmId'        => $model->firm_id,
            'classN'        => $model->formName()]),
    ],
    [
        'label'   => 'Юр.Лица',
        'content' => $this->render('_ourfirm_rekvizit', [
            'dataProvaider' => $provaiderRekvizit,
            'firmId'        => $model->firm_id,
            'classN'        => $model->formName()])
    ],
//    [
//        'label'=>'Менеджеры',
//        'content'=>$this->render('managersList',['dataProvaider'=>$provaiderManagers, 'firmId'=>$model->firm_id,'classN'=>$model->formName()]),
//        'disabled'=>true
//    ],
    [
        'label'   => 'Доставка',
        'content' => $this->render('shippingList' . $model->formName(), [
            'dataProvaider' => $provaiderShipping,
            'firmId'        => $model->firm_id,
            'classN'        => $model->formName()]),
    ],
];
if ($model->formName() === 'Post' || $model->formName() === 'Pod') {
    $items[] = [
        'label'   => $model->formName() === 'Pod' ? 'Вид работ' : 'Вид материала',
        'content' => $this->render('wopList', [
            'dataProvaider' => $provaiderWOP,
            'firmId'        => $model->firm_id,
            'classN'        => $model->formName()]),
    ];
}
?>
<div class="dialog-list">
    <div class="dialog-row">
        <div class="dialog-control-group center-block">
            <label><?= $model->attributeLabels()['mainName'] ?></label>
            <?=
            Html::activeTextInput($model, 'mainName', [
                'disabled' => true])
            ?>
            <?=
            Html::hiddenInput('firm_id', $model->firm_id, [
                'id'    => 'curfirm_id',
                'style' => 'display:none']);
            ?>
        </div>
    </div>
    <div class="dialog-row">
        <?=
        Tabs::widget([
            'id'                => 'firm-our-list-tab',
            'tabContentOptions' => [
                'class' => 'tab-content firm-tab',
            ],
            'items'             => $items
        ])
        ?>
    </div>
    <?=
    Html::hiddenInput('firmId', $model->firm_id, [
        'id' => 'current_firmId']);
    ?>
</div>

