<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 *
 * @var $this yii\web\View
 * @var $model app\models\zakaz\Zakaz
 */

use yii\helpers\Html;

$colorsType = ['по пантону', 'примерный', 'по образцу'];
$materialPos = ['Купить', 'На складе', 'У заказчика'];
$materialCount = $model->isMaterialSet;
?>
<?php for ($i = 0; $i < $materialCount && $i<2; $i++): ?>
    <?php $key = $i ? $i : ''; ?>
    <tr class="bold">
        <th>Материал:</th>
        <td colspan="2" class="rt"><div class="t-danger"><?= $model->getHeaderName($i) ?></div></td>
        <td colspan="5"><div class="ramka"><?= $model->materialColumn('Название', $i) ?></div></td>
        <td rowspan="4" colspan="3" class="btt blt gr"><?= $model->blockInfo($i) ?></td>
    </tr>
    <tr class="bold">
        <th><?if (!$model->isOurMaterial($i)):?><div class="ramka md"><?= $model->materialColumn('Чей', $i) ?></div><?endif;?></th>
        <td colspan="2" class="rt sm">Цвет</td>
        <td colspan="5"><div class="ramka"><?= $model->materialColumn('Цвет', $i) ?></div></td>
    </tr>
    <tr class="bold">
        <th><div class="ramka md"><?= $model["date_of_receipt$key"] ? Yii::$app->formatter->asDate($model["date_of_receipt$key"]) : '' ?></div></th>
        <td colspan="2" class="rt">Плотность</td>
        <td colspan="1"><div class="ramka"><?= $model->materialColumn('Плотность', $i) ?></div></td>
        <td></td>
        <td colspan="1" class="rt">Размер листа</td>
        <td colspan="2" class="md"><div class="ramka"><?= $model->materialColumn('Размер листа', $i) ?></div></td>

    </tr>
    <tr class="btt bold">
        <th class="btt"><div class="ramka md"><?= isset($materialPos[$model["material_is_on_sklad$key"]])?$materialPos[$model["material_is_on_sklad$key"]]:"" ?></div></th>
        <td colspan="2" class="rt">Кол-во листов</td>
        <td colspan="1"><div class="ramka md"><?= $model->materialColumn('Количество', $i) ?></div></td>
        <td></td>
        <td class="rt" colspan="1">Раскрой листа</td>
        <td colspan="2"><div class="ramka md"><?= $model->raskroyLista($i) ?></div></td>

    </tr>
<?php endfor; ?>
<tr>
    <th class="">Печать:</th>
    <td class="rt" style="width: 50px;">Цвет</td>
    <td colspan="5"><div class="ramka gr"><?= $model->colorFaceCount || $model->colorBackCount ? $colorsType[$model->post_print_color_fit] : '' ?></div></td>
    <td colspan="4"></td>
</tr>
<tr>
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md t-danger">Вызов на печать</div><?php endif; ?></th>

    <td><div class="ramka gr"><?= $model->colorFaceCount ?></div></td>
    <td colspan="9"><div class="ramka"><?= $model->colorFaceString ?></div></td>
</tr>
<tr>
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md"><?= $model->post_print_agent_name ?></div><?php endif; ?></th>
    <td><div class="ramka gr"><?= $model->colorBackCount ?></div></td>
    <td colspan="9"><div class="ramka"><?= $model->colorBackString ?></div></td>
</tr>
<tr>
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md"><?= $model->post_print_agent_phone ?></div><?php endif; ?></th>
    <td colspan="2"><?= $model->hasUfLak ? 'Уф-лак' : '' ?></td>
    <td><?if ($model->hasUfLak):?><div class="ramka"><?= $model->ufLakText ?></div><?endif?></td>
    <td colspan="3"><?if ($model->hasUfLak):?><div class="ramka"><?= $model->post_print_uf_lak ? $model->post_print_uf_lak_text : '' ?></div><?endif;?></td>
    <td colspan="4"></td>
</tr>
<?php if ($model->hasThermalLift): ?>
    <tr>
        <th></th>
        <td colspan="2">Термоподъём</td>
        <td><div class="ramka"><?= $model->thermalLiftText ?></div></td>
        <td colspan="3"><div class="ramka" <?= mb_strlen($model->post_print_thermal_lift_text) > 32 ? ('title="' . $model->post_print_thermal_lift_text . '" style="overflow: hidden;max-width: 200px;text-overflow: ellipsis"') : '' ?>><?= $model->post_print_thermal_lift ? $model->post_print_thermal_lift_text : '' ?></div></td>
        <td></td>
        <td colspan="2"></td>
    </tr>
<?php endif; ?>
<tr class="btt">
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md"><?= $model->post_print_firm_name ?></div><?php endif; ?></th>
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
