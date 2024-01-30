<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$materialCount = $model->isMaterialSet;
$materialPos = ['Купить', 'На складе', 'У заказчика'];
$colorsType = ['по пантону', 'примерный', 'по образцу'];
?>

<?php for ($i = 0; $i < $materialCount; $i++): ?>
    <?php $key = $i ? $i : ''; ?>
    <tr class="bold">
        <th>Материал:</th>
        <td colspan="3" class="rt"><div class="t-danger"><?= $model->headerName ?></div></td>
        <td colspan="4"></td>
        <td rowspan="4" colspan="3" class="btt blt gr"><?= $model->blockInfo($i) ?></td>
    </tr>
    <tr class="bold">
        <th><?if (!$model->isOurMaterial($i)):?><div class="ramka md"><?= $model->materialColumn('Чей', $i) ?></div><?endif;?></th>
        <td colspan="1" class="rt sm">Цвет</td>
        <td colspan="6"><div class="ramka"><?= $model->materialColumn('Цвет', $i) ?></div></td>
    </tr>
    <tr class="bold">
        <th><div class="ramka md"><?= $model["date_of_receipt$key"] ? Yii::$app->formatter->asDate($model["date_of_receipt$key"]) : '' ?></div></th>
        <td colspan="1" class="rt sm">Размер</td>
        <td colspan="2"><div class="ramka"><?= $model->materialColumn('Размер', $i) ?></div></td>
        <td class="rt sm">Плотность</td>
        <td><div class="ramka"><?= $model->materialColumn('Плотность', $i) ?></div></td>
        <td colspan="2">Количество</td>
    </tr>
    <tr class="btt bold">
        <th class="btt"><div class="ramka md"><?= $materialPos[$model["material_is_on_sklad$key"]] ?></div></th>
        <td colspan="5"></td>
        <td><div class="ramka"><?= $model->materialColumn('Количество', $i) ?></div></td>
        <td colspan="1"></td>
    </tr>
<?php endfor; ?>
<tr>
    <th>Печать:</th>
    <td class="rt">Краски</td>
    <td colspan="4"><div class="ramka gr"><?= $colorsType[$model->post_print_color_fit] ?></div></td>
    <td colspan="5"></td>
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
    <td></td>
    <td><?= $model->hasUfLak ? 'Уф-лак' : '' ?></td>
    <td><?if ($model->hasUfLak):?><div class="ramka"><?= $model->ufLakText ?></div><?endif?></td>
    <td colspan="2"><?if ($model->hasUfLak):?><div class="ramka"><?= $model->post_print_uf_lak_text ?></div><?endif?></td>
    <td></td>
    <td><?= $model->hasThermalLift ? 'Термоподъём' : '' ?></td>
    <td><?if ($model->hasThermalLift):?><div class="ramka"><?= $model->thermalLiftText ?></div><?endif?></td>
    <td colspan="2"><?if ($model->hasThermalLift):?><div class="ramka"><?= $model->post_print_thermal_lift_text ?></div><?endif?></td>
</tr>
<tr class="btt">
    <th><?php if ($model->post_print_call_to_print): ?><div class="ramka md"><?= $model->post_print_firm_name ?></div><?php endif; ?></th>
    <td></td>
    <td colspan="1" class="sm">Примечания к печати:</td>
    <td colspan="8"><div class="ramka gr wrow"><?= $model->noteHtml ?></div></td>
</tr>
