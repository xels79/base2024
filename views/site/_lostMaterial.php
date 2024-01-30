<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $model array
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="panel-heading" role="tab" id="headingMain<?= $index ?>">
    <h3 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion_main" href="#collapseMain<?= $index ?>" aria-expanded="true" aria-controls="collapseMain<?= $index ?>">
            К заказу №<?= $model['zId']; ?>
        </a>
    </h3>

</div>
<div id = "collapseMain<?= $index ?>" class = "panel-collapse collapse" role = "tabpanel" aria-labelledby = "headingMain<?= $index ?>">
    <div class = "panel-body">
        <div class = "panel-group" id = "accordion<?= $index ?>" role = "tablist" aria-multiselectable = "true">
            <p><?= Html::a('Удалить', ['checkmaterials', 'idToErase' => $model['id']]) ?></p>
            <p>Поставщик - "<?= $model['fName'] ?>"</p>
            <p>Название материала - "<?= $model['mName'] ?>"</p>
        </div>
    </div>
</div>