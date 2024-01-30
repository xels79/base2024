<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $this yii\web\View
 */
use yii\widgets\Pjax;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
?>
<?php if (!Yii::$app->request->isPjax):?>
<div class="m-row">
    <div id="chartContainer2" style="height: 700px; width: 95%;"></div>
</div>
<div class="m-row">
    <div id="chartContainer3" style="height: 700px; width: 95%;"></div>
</div>
<?php endif;?>
<?php Pjax::begin([
    'id'=>'firstGraphicsPjax'
]);
    $form = ActiveForm::begin([
        'id' => 'graphic-form',
        'options' => ['data' => ['pjax' => true],'class'=>'gr-form'],
    ]) ?>
        <?=$form->field($gModel, 'year')->dropdownList($gModel->yearList,['prompt'=>'Выберите год'])->label('Показать графики за год...')?>
        <?php if (!$gModel->hasErrors() && $gModel->year>0):?>
            <div class="m-row"><div id="chartContainer4" style="height: 700px; width: 95%;" data-value="<?=htmlspecialchars(Json::encode($managers_exists))?>" data-year="<?=$selectedYear?>"></div></div>
            <div class="m-row"><div id="chartContainer5" style="height: 700px; width: 95%;" data-value="<?=htmlspecialchars(Json::encode($total))?>"></div></div>
        <?php endif;?>
    <?php ActiveForm::end() ?>
<?php Pjax::end();?>

