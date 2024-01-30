<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//use yii;
use yii\helpers\Html;

$zp = 0;
$prize = 0;
$minus = 0;
$card1 = 0;
$card2 = 0;
$payment1 = 0;
$payment2 = 0;
$payment3 = 0;
$summ = 0;
$drawFooter = false;
?>
<?php foreach ($payment_ids as $payment_id): ?>
    <?php if ($idHtml === false && $payment_id === 1): ?>
        <tr class="separator">
            <th>Зарплата</th>
            <th class="space2">&nbsp;</th>
            <td colspan="2"></td>
            <td>дни</td>
            <td colspan="3"></td>
            <td class="space">&nbsp;</td>
            <td colspan="5"></td>
            <td class="summ"></td>
            <td></td>
        </tr>
    <?php elseif ($idHtml === false && $payment_id === 2): ?>
        <tr class="separator">
            <th>Оклад</th>
            <th class="space2">&nbsp;</th>
            <td colspan="6"></td>
            <td class="space">&nbsp;</td>
            <td colspan="5"></td>
            <td class="summ"></td>
            <td></td>
        </tr>
    <?php elseif ($idHtml === false && $payment_id === 3): ?>
        <tr class="separator">
            <th>Сделка</th>
            <th class="space2">&nbsp;</th>
            <td></td>
            <td>руб/пр</td>
            <td>прокаты</td>
            <td colspan="3"></td>
            <td class="space">&nbsp;</td>
            <td colspan="5"></td>
            <td class="summ"></td>
            <td></td>
        </tr>
    <?php elseif ($idHtml === false && $payment_id === 4): ?>
        <tr class="separator">
            <th>%</th>
            <th class="space2">&nbsp;</th>
            <td colspan="6"></td>
            <td class="space">&nbsp;</td>
            <td colspan="5"></td>
            <td class="summ"></td>
            <td></td>
        </tr>

    <?php endif; ?>
    <?php if (array_key_exists($payment_id, $zarplata)): ?>
        <?php foreach ($zarplata[$payment_id] as $m): ?>
            <?php
            $zp += $payment_id === 4 ? $m['prize'] : $m->wagesText;
            $prize += $m['prize'];//$payment_id === 2 || $payment_id === 4 ? 0 : $m['prize'];
            $minus += $m['minus'];//$payment_id === 2 || $payment_id === 4 ? 0 : $m['minus'];
            //if ($m['employed'] == 1) {
                $card1 += $m['card1'] ? $m['card1'] : 0;
                $card2 += $m['card2'] ? $m['card2'] : 0;
            //}
            $payment1 += $payment_id === 2 ? 0 : $m['payment1'];
            $payment2 += $m['payment2'];
            $payment3 += $m['payment3'];
            $summ += $m->summ;
            if (!$drawFooter) {
                $drawFooter = $idHtml === false || $idHtml == $m['id'];
            }
            ?>
            <?php if ($idHtml === false || $idHtml == $m['id']): ?>
                <tr class="with-date" data-month-id="<?= $month_id ?>" data-zarplata-key="<?= $m['id'] ?>">
                    <td><?= $m['name'] ?></td>
                    <th class="space2">&nbsp;</th>
                    <td><?= $payment_id > 2 ? '' : $m['wages'] ? Yii::$app->formatter->asCurrency($m['wages']) : '' ?></td>
                    <td><?= $m->normalText ? Yii::$app->formatter->asCurrency($m->normalText) : '' ?></td>
                    <td><?=
                        $payment_id === 2 || $payment_id === 4 ? '' : Html::input('text', 'hours', $m['hours'] ? round($m['hours'], 2) : 0, [
                                    'role' => 'data-input',
									'data-allow-point'=>(($idHtml === false && $payment_id === 0)?'true':'false')])
                        ?></td>
                    <td><?=
                        $payment_id === 4 ? Html::input('text', 'prize', $m['prize'] ? round($m['prize'], 2) : 0, [
                                    'role' => 'data-input']) : Yii::$app->formatter->asCurrency($m->wagesText)
                        ?></td>
                    <td><?=
                        $payment_id === 4 && false ? '' : Html::input('text', 'prize', $m['prize'] ? round($m['prize'], 2) : 0, [
                                    'role' => 'data-input'])
                        ?></td>
                    <td><?=
                        $payment_id === 4 && false ? '' : Html::input('text', 'minus', $m['minus'] ? round($m['minus'], 2) : 0, [
                                    'role' => 'data-input'])
                        ?></td>
                    <td class="space">&nbsp;</td>
                    <?php if ($payment_id !== 4):// ||  $m['employed'] == 1): ?>
                        <td><?=
                            Html::input('text', 'card1', $m['card1'] ? round($m['card1'], 2) : 0, [
                                'role' => 'data-input'])
                            ?></td>
                        <td><?=
                            Html::input('text', 'card2', $m['card2'] ? round($m['card2'], 2) : 0, [
                                'role' => 'data-input'])
                            ?></td>
                    <?php else: ?>
                        <td></td>
                        <td></td>
                    <?php endif; ?>
                    <td><?=
                        $payment_id === 2 ? '' : Html::input('text', 'payment1', $m['payment1'] ? round($m['payment1'], 2) : 0, [
                                    'role' => 'data-input'])
                        ?></td>
                    <td><?=
                        Html::input('text', 'payment2', $m['payment2'] ? round($m['payment2'], 2) : 0, [
                            'role' => 'data-input'])
                        ?></td>
                    <td><?=
                        Html::input('text', 'payment3', $m['payment3'] ? round($m['payment3'], 2) : 0, [
                            'role' => 'data-input'])
                        ?></td>
                    <td<?= $m->getSumm($payment_id) > 0 ? ' class="ostatok"' : ($m->getSumm($payment_id) < 0 ? ' class="ostatok-minus"' : '') ?>><?= Yii::$app->formatter->asCurrency($m->getSumm($payment_id)) ?></td>
                    <td><?=
                        Html::input('text', 'comment', $m['comment'], [
                            'role' => 'data-input'])
                        ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($drawFooter): ?>
    <tr class="summ">
        <th colspan="5">Итог</th>
        <td><?= Yii::$app->formatter->asCurrency($zp) ?></td>
        <td><?= Yii::$app->formatter->asCurrency($prize) ?></td>
        <td><?= Yii::$app->formatter->asCurrency($minus) ?></td>
        <td class="space">&nbsp;</td>
        <td><?= Yii::$app->formatter->asCurrency($card1) ?></td>
        <td><?= Yii::$app->formatter->asCurrency($card2) ?></td>
        <td><?= Yii::$app->formatter->asCurrency($payment1) ?></td>
        <td><?= Yii::$app->formatter->asCurrency($payment2) ?></td>
        <td><?= Yii::$app->formatter->asCurrency($payment3) ?></td>
        <td class="summ"><?= Yii::$app->formatter->asCurrency($summ) ?></td>
        <td></td>
    </tr>
<?php endif; ?>

