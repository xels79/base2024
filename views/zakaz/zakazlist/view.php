<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<?=!$asDialog?Html::beginTag('div',['class'=>'container-fluid']):''?>
<table class="info-view specification">
    <tr>
        <td rowspan="3">
            <div class="panel panel-info">
                <?=!$asDialog?Html::tag('div',"Подродности заказа №$model->id",['class'=>'panel-heading']):''?>
                <div class="panel-body">
                    <?=DetailView::widget([
                        'model'=>$model,
                        'attributes'=>[
                            'id',
                            'ourmanagername',
                            'dateofadmission:date',
                            'zak_idText',
                            'production_idText',
                            'name',
                            'number_of_copiesText',
                            'product_size',
                            'specification:raw',
                            'podryadview:raw',
                            'total_coast:decimal',
                            'total_spending:decimal',
                            'deadline:date'
                        ]
                    ])?>
                </div>
            </div>
        </td>
        <td>
            <div class="panel panel-info  hidden-print">
                <div class="panel-heading">Файлы заказчика</div>
                <div class="panel-body">
                    <?=app\components\MyHelplers::renderFileListById($model->id)?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="panel panel-info  hidden-print">
                <div class="panel-heading">Файлы дизайнера</div>
                <div class="panel-body">
                    <?=app\components\MyHelplers::renderFileListById($model->id,'des')?>
                </div>
            </div>
        </td>
    </tr>
    <tr><td height="100%">&nbsp;</td></tr>
</table>
<?=!$asDialog?(Html::endTag('div').'<br><br>'):''?>