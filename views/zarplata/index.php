<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Tabs;

$this->title = 'Зарплата';
?>
<div class="zarplata">
    <?= $alert ?>
    <div class="zarplata-contols">
        <div class="input-group">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="zarplata-get-yaer" disabled="disabled">Показать за</button>
            </span>
            <input type="number" class="form-control" name="year" id="zarpalata-yaer" disabled="disabled" value="<?= $year ?>">
            <span class="input-group-addon">год</span>
        </div>
    </div>
    <div class="zarplata-tabs">
        <?=
        Tabs::widget([
            'items'   => $items,
            'options' => ['id' => 'zarplateTabs']
        ])
        ?>
    </div>
</div>
<?= ''//\yii\helpers\VarDumper::dumpAsString($otheMonths, 10, true) ?>