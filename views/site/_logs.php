<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $model array
 */
?>
<div class="panel-heading" role="tab" id="headingMain<?= $index ?>">
    <h3 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion_main" href="#collapseMain<?= $index ?>" aria-expanded="true" aria-controls="collapseMain<?= $index ?>"><?= Yii::$app->formatter->asDatetime($model['time']) ?>&nbsp-&nbsp;<?= $model['header'] ?>&nbsp;<?= $model['message']['subHeader'] ?></a>
    </h3>

</div>
<div id="collapseMain<?= $index ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingMain<?= $index ?>">
    <div class="panel-body">
        <div class="panel-group" id="accordion<?= $index ?>" role="tablist" aria-multiselectable="true">
            <?php foreach ($model['message']['data'] as $key => $val): ?>
                <div class="panel panel-info">
                    <div class="panel-heading" role="tab" id="heading<?= $key . $index ?>">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion<?= $index ?>" href="#collapse<?= $key . $index ?>" aria-expanded="true" aria-controls="collapseOne<?= $key . $index ?>">
                                Переменная "<?= $key ?>"...
                            </a>
                        </h4>
                    </div>
                    <div id="collapse<?= $key . $index ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?= $key . $index ?>">
                        <div class="panel-body">
                            <p><?= yii\helpers\VarDumper::dumpAsString($val, 10, true) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>