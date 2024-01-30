<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 *
 * @var $this yii\web\View
 * @var $model app\models\zakaz\Zakaz
 */

$materialCount = $model->isMaterialSet;
$dt = \yii\helpers\Json::decode($model->colors);
?>
<?php for ($i = 0; $i < $materialCount; $i++): ?>
    <?php $key = $i ? $i : ''; ?>
    <tr class="bold">
        <th>Материал:</th>
        <td colspan="2" class="rt"><div class="t-danger"><?= $model->getHeaderName($i) ?></div></td>
        <td colspan="5"><div class="ramka"><?= $model->materialColumn('Название', $i) ?></div></td>
        <td rowspan="4" colspan="3" class="btt blt gr"><?= $model->blockInfo($i) ?></td>
    </tr>
    <tr class="bold">
        <th><?if (!$model->isOurMaterial($i)):?><div class="ramka md"><?= $model->materialColumn('Чей', $i) ?></div><?endif;?></th>
        <td colspan="2" class="rt sm">Размер листа</td>
        <td colspan="2"><div class="ramka"><?= $model->materialColumn('Размер листа', $i) ?></div></td>
        <td class="rt sm">Плотность</td>
        <td><div class="ramka"><?= $model->materialColumn('Плотность', $i) ?></div></td>
        <td colspan="1">Кол-во листов</td>
    </tr>
    <tr class="bold">
        <th><div class="ramka md"><?= $model["date_of_receipt$key"] ? Yii::$app->formatter->asDate($model["date_of_receipt$key"]) : '' ?></div></th>
        <td colspan="2">Коментарий: </td>
        <td colspan="4"><div class="ramka"><?= is_array($dt['rem']) && array_key_exists($i, $dt['rem']) ? $dt['rem'][$i] : '' ?></div></td>
        <td colspan="1"><div class="ramka md"><?= $model->materialColumn('Количество', $i) ?></div></td>
    </tr>
    <tr class="btt bold">
        <th><div class="ramka md"><?= $materialPos[$model["material_is_on_sklad$key"]] ?></div></th>
        <td colspan="7" class="rt sm"></td>
    </tr>
<?php endfor; ?>
<tr class=" bold">
    <th class="">Печать:</th>
    <td class="t-danger" colspan="2">Уф-лак:</td>
    <td></td>
    <td></td>
    <td class="t-danger" colspan="4">Подложка:</td>
    <td class="t-danger"></td>
</tr>
<tr>
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md t-danger">Вызов на печать</div><?php endif; ?></th>
    <td><div class="ramka gr"><?= $dt['face_id'] ? 1 : 0 ?></div></td>
    <td colspam="3"><div class="ramka"><?= is_array($dt['face_values']) && $dt['face_values'][0] ? $dt['face_values'][0] : '' ?></div></td>
    <td></td>
    <td></td>
    <td><div class="ramka gr"><?= $dt['back_id'] ? 'Да' : 'Нет' ?></div></td>
    <td colspam="2"><div class="ramka"><?= is_array($dt['back_values']) && $dt['back_values'][0] ? $dt['back_values'][0] : '' ?></div></td>
    <td colspan="1"></td>
</tr>
<tr>
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md"><?= $model->post_print_agent_name ?></div><?php endif; ?></th>
    <td><div class="ramka gr"><?= $dt['side_id'] ? 1 : 0 ?></div></td>
    <td colspam="2"><div class="ramka"><?= $dt['side_id'] && is_array($dt['side_values']) && $dt['side_values'][0] ? $dt['side_values'][0] : '' ?></div></td>
    <td></td>
    <td></td>
    <td><div class="ramka gr"><?= $dt['clips_id'] ? 'Да' : 'Нет' ?></div></td>
    <td colspam="2"><div class="ramka"><?= $dt['clips_id'] && is_array($dt['clips_values']) && $dt['clips_values'][0] ? $dt['clips_values'][0] : '' ?></div></td>
    <td colspan="1"></td>
</tr>
<tr>
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md"><?= $model->post_print_agent_phone ?></div><?php endif; ?></th>
    <td class="t-danger bold" colspan="2">Верный угол:</td>
    <td><div class="ramka gr"><?= $dt['ang_id'] ? 'Да' : 'Нет' ?></div></td>
    <td colspan="6"><div class="ramka"><?= !$dt['ang_id'] ? (is_array($dt['ang_values']) && $dt['ang_values'][0] ? $dt['ang_values'][0] : '') : '' ?></div></td>
</tr>
<tr class="btt">
    <th style="vertical-align:top;"><?php if ($model->post_print_call_to_print): ?><div class="ramka md"><?= $model->post_print_firm_name ?></div><?php endif; ?></th>
    <td colspan="3" class="sm">Примечания к печати:</td>
    <td colspan="7"><div class="ramka gr wrow"><?= $model->noteHtml ?></div></td>
</tr>
<?= $model->getPostPrintRawsNew() ?>
<tr class="btt bold">
    <th>Резка:</th>
    <?php if ($model->post_print_rezka): ?>
        <td><div class="ramka t-danger">Резать</div></td>
    <?php else: ?>
        <td><div class="ramka t-danger">НЕ РЕЗАТЬ!</div></td>
    <?php endif; ?>
    <td colspan="9"><div class="ramka"><?= $model->post_print_rezka ? $model->post_print_rezka_text : $model->post_print_rezka_text ?></div></td>
</tr>
