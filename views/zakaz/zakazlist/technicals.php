<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
?>
<table class="info-view tecnikals">
    <tr>
        <td rowspan="<?= $technicalsPrin ? 1 : 3 ?>">
            <div class="panel panel-info">
                <?= !$asDialog ? Html::tag('div', "Подробности заказа №$model->id", ['class' => 'panel-heading']) : '' ?>
                <div class="panel-body">
                    <?= $this->render('t-newvar', ['model' => $model, 'technicalsPrin' => true]) ?>

                </div>
            </div>
        </td>
        <?php if (!$technicalsPrin): ?>
            <td>
                <div class="panel panel-info hidden-print">
                    <div class="panel-heading">Файлы заказчика</div>
                    <div class="panel-body">
                        <?= app\components\MyHelplers::renderFileListById($model->id) ?>
                    </div>
                </div>
            </td>
        <?php endif; ?>
    </tr>
    <?php if (!$technicalsPrin): ?>
        <tr>
            <td>
                <div class="panel panel-info hidden-print">
                    <div class="panel-heading">Файлы дизайнера</div>
                    <div class="panel-body">
                        <?= app\components\MyHelplers::renderFileListById($model->id, 'des') ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php endif; ?>
    <tr><td height="100%">&nbsp;</td></tr>
</table>
<?=
!$asDialog ? (Html::endTag('div') . '<br><br>') : ''?>