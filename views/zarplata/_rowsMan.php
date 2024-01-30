<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//use yii;
use yii\helpers\Html;
?>
<?php foreach ($mPostArr as $mPost): ?>
    <?php if (array_key_exists($mPost, $month['zarplata'])): ?>
        <?php foreach ($month['zarplata'][$mPost] as $m): ?>
            <?= \yii\helpers\VarDumper::dumpAsString($m, 10, true) ?>
            <tr class="with-date" data-month-id="<?= $months['id'] ?>" data-zarplata-key="<?= $m['id'] ?>">
                <td><?= $m['name'] ?></td>
                <td><?= $m['wages'] ? Yii::$app->formatter->asCurrency($m['wages']) : '' ?></td>
                <td><?= $m['normal'] ? Yii::$app->formatter->asCurrency($m['normal']) : '' ?></td>
                <td><?= mb_strpos($m['name'], '%') === false ? Html::input('number', 'hours' . $months['id'], $m['hours'] ? $m['hours'] : 0) : '' ?></td>
                <td>з/п</td>
                <td>премия</td>
                <td class="space">&nbsp;</td>
                <td>22 число</td>
                <td>Выдано</td>
                <td>07 число</td>
                <td>Выдано</td>
                <td>Остаток</td>
                <td>Выдано</td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>
