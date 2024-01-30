<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $this yii\web\View
 * @var $managers_exists array
 * $var $managers array
 * @var $periud int
 */
$mnCnt = 1;
?>
<form method="post" id="select-form" class="hidden-print">
    <div class="row">
        <div class="col-md-1 text-right">Менеджер:</div>
        <?php foreach ($managers_exists as $el): ?>
            <?php if ($mnCnt / 5 === 1): ?>
            </div>
            <div class="row">
            <?php endif; ?>
            <div class="col-md-1 <?= ($mnCnt / 5 === 1) ? ' col-md-offset-1' : '' ?>">
                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox<?= $mnCnt++ ?>" value="on" name="managers[<?= $el['id'] ?>]"<?= in_array($el['id'], array_keys($managers)) ? ' checked' : '' ?>><?= $el['realname'] ?>
                    </label>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</form>
<?php if (!Yii::$app->request->isPjax):?>
<div class="m-row">
    <div id="chartContainer" style="height: 400px; width: 90%;"></div>
</div>
<?php endif;?>

