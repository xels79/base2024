<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\bootstrap\Tabs;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

Pjax::begin([
    'options'=>['id'=>'s3ChangeYaer-pjax','data-reinit-target'=>'s3selyer-year'],
    'formSelector'=>'#formTablesSelectYear',
    'timeout'=>7000
    ]);
   $form = ActiveForm::begin([
        'id' => 'formTablesSelectYear',
        'options' => ['data' => ['pjax' => true],'class'=>'gr-form','style'=>'margin-top:10px;'],
    ]);
?>
    <?=$form->field($gModel2, 'year')->dropdownList($gModel2->yearList)->label('Показать таблицу за год...')?>
    <?=$form->field($gModel2, 'columnType')->dropdownList($gModel2->columnTypesList)->label('Тип графика...')?>
<?php ActiveForm::end(); ?>
<div id="svodn-graph"
     style="margin-top:10px;min-height:400px;height:400px;width:65%;"
     data-value="<?=htmlspecialchars(Json::encode($crossGraph['total']))?>"
     data-year="<?=$gModel2->year?>"
></div>
<?=Tabs::widget([
    'id'=>'Table1Tabs',
    'items' => $crossGraph['items'],
    'options'=>[
        'style'=>'margin-top:20px;'
    ]
]);?>
<?php Pjax::end();?>