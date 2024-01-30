<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\bootstrap\Tabs;
?>
<div class="dialog-list">
    <div class="dialog-row">
        <div class="dialog-control-group center-block">
            <label><?= $model->attributeLabels()['mainName'] ?></label>
            <?=
            Html::activeTextInput( $model, 'mainName', [
                'disabled' => true] )
            ?>
            <?=
            Html::hiddenInput( 'firm_id', $model->firm_id, [
                'id'    => 'ourfirm_id',
                'style' => 'display:none'] );
            ?>
        </div>
    </div>
    <div class="dialog-row">
        <?=
        Tabs::widget( [
            'id'                => 'firm-our-list-tab',
            'tabContentOptions' => [
                'class' => 'tab-content firm-tab',
            ],
            'items'             => [
                [
                    'label'   => 'Юр.Лица',
                    'content' => $this->render( '_ourfirm_rekvizit', [
                        'dataProvaider' => $provaiderRekvizit,
                        'firmId'        => $model->firm_id] )
                ],
                [
                    'label'   => 'Адреса',
                    'content' => $this->render( '_addresses', [
                        'dataProvaider' => $provaiderAdresses,
                        'firmId'        => $model->firm_id] ),
                ],
                [
                    'label'   => 'Менеджеры',
                    'content' => $this->render( '_managers', [
                        'dataProvaider' => $provaiderManagers,
                        'firmId'        => $model->firm_id] ),
                ],
                [
                    'label'   => 'Сотрудники',
                    'content' => $this->render( '_managers', [
                        'dataProvaider' => $provaiderEmployee,
                        'firmId'        => $model->firm_id,
                        'pjaxId'        => 'ourEmployeeListPjax'] ),
                ],
                [
                    'label'   => 'Логотип',
                    'content' => $this->render( '_piclogoourfirm', [
                        'model' => $model] ),
                ],
            ]
        ] )
        ?>
    </div>
    <?=
    Html::hiddenInput( 'firmId', $model->firm_id, [
        'id' => 'current_firmId'] );
    ?>
</div>

