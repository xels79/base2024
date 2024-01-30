<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$colorsType = ['по пантону', 'примерный', 'по образцу'];
$materialCount = $model->isMaterialSet;
?>

<?php for ($i = 0; $i < $materialCount; $i++): ?>
    <tr class="bold">
        <th>Материал:</th>
        <td colspan="3" class="rt"><div class="t-danger"><?= $model->getHeaderName($i) ?></div></td>
        <td colspan="4"><div class="ramka"><?= $model->materialColumn('Название', $i) ?></div></td>
        <td rowspan="4" colspan="3" class="btt blt gr"><?= $model->blockInfo($i) ?></td>
    </tr>
    <tr class="bold">
        <th><?if (!$model->isOurMaterial($i)):?><div class="ramka md"><?= $model->materialColumn('Чей', $i) ?></div><?endif;?></th>
        <td colspan="2" class="rt sm">Способ печати</td>
        <td colspan="5"><div class="ramka"><?= $model->worktypes_id_text ?></div></td>
    </tr>
    <tr class="bold">
        <th><div class="ramka md"><?= $model->date_of_receipt ? Yii::$app->formatter->asDate($model->date_of_receipt) : '' ?></div></th>
        <td colspan="1" class="rt sm">Состав:</td>
        <td colspan="2"><div class="ramka"><?= $model->materialColumn('Cостав', $i) ?></div></td>
        <td class="rt sm">Цвет:</td>
        <td colspan="2"><div class="ramka"><?= $model->materialColumn('Цвет', $i) ?></div></td>
        <td>Количество</td>
    </tr>
    <tr class="btt bold">
        <th class="btt"><div class="ramka md"><?= $model['material_is_on_sklad' . ($i ? $i : '')] ? 'На складе' : 'Купить' ?></div></th>
        <td colspan="6"></td>
        <td><div class="ramka"><?= $model->materialColumn('Количество', $i) ?></div></td>
    </tr>
<?php endfor; ?>
<tr>
    <th>Печать:</th>
    <td class="rt">Краски</td>
    <td colspan="4"><div class="ramka gr"><?= $colorsType[$model->post_print_color_fit] ?></div></td>
    <td colspan="5"></td>
</tr>
<tr>
    <?php if ($model->post_print_call_to_print): ?>
        <th><div class="ramka md t-danger">Вызов на печать</div></th>
        <?php if ($model->colorFaceCount): ?>
            <td><div class="ramka gr"><?= $model->colorFaceCount ?></div></td>
            <td><div class="ramka"><?= $model->colorFaceText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorFaceString ?></div></td>
        <?php else: ?>
            <td colspan="10">&nbsp;</td>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($model->colorFaceCount): ?>
            <th></th>
            <td><div class="ramka gr"><?= $model->colorFaceCount ?></div></td>
            <td><div class="ramka"><?= $model->colorFaceText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorFaceString ?></div></td>
        <?php endif; ?>
    <?php endif; ?>
</tr>
<tr>
    <?php if ($model->post_print_call_to_print): ?>
        <th><div class="ramka md"><?= $model->post_print_agent_name ?></div></th>
        <?php if ($model->colorBackCount): ?>
            <td><div class="ramka gr"><?= $model->colorBackCount ?></div></td>
            <td><div class="ramka"><?= $model->colorBackText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorBackString ?></div></td>
        <?php else: ?>
            <td colspan="10">&nbsp;</td>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($model->colorBackCount): ?>
            <th></th>
            <td><div class="ramka gr"><?= $model->colorBackCount ?></div></td>
            <td><div class="ramka"><?= $model->colorBackText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorBackString ?></div></td>
        <?php endif; ?>
    <?php endif; ?>
</tr>
<tr>
    <?php if ($model->post_print_call_to_print): ?>
        <th><div class="ramka md"><?= $model->post_print_agent_phone ?></div></th>
        <?php if ($model->colorClipsCount): ?>
            <td><div class="ramka gr"><?= $model->colorClipsCount ?></div></td>
            <td><div class="ramka"><?= $model->colorClipsText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorClipsString ?></div></td>
        <?php else: ?>
            <td colspan="10">&nbsp;</td>
        <?php endif ?>
    <?php else: ?>
        <?php if ($model->colorClipsCount): ?>
            <th></th>
            <td><div class="ramka gr"><?= $model->colorClipsCount ?></div></td>
            <td><div class="ramka"><?= $model->colorClipsText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorClipsString ?></div></td>
        <?php endif; ?>
    <?php endif; ?>
</tr>
<tr>
    <?php if ($model->post_print_call_to_print): ?>
        <th><div class="ramka md"><?= $model->post_print_firm_name ?></div></th>
        <?php if ($model->colorSideCount): ?>
            <td><div class="ramka gr"><?= $model->colorSideCount ?></div></td>
            <td><div class="ramka"><?= $model->colorSideText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorSideString ?></div></td>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($model->colorSideCount): ?>
            <th></th>
            <td><div class="ramka gr"><?= $model->colorSideCount ?></div></td>
            <td><div class="ramka"><?= $model->colorSideText ?></div></td>
            <td colspan="8"><div class="ramka"><?= $model->colorSideString ?></div></td>
        <?php endif; ?>
    <?php endif; ?>
</tr>

<tr class="btt">
    <th></th>
    <td colspan="1" class="sm">Примечания к печати:</td>
    <td colspan="9"><div class="ramka gr wrow"><?= $model->noteHtml ?></div></td>
</tr>
<?php if ($model->post_print_uf_lak || $model->post_print_thermal_lift): ?>
    <tr class="btt bold">
        <th>Расп/Упак:</th>
        <td colspan="11"><div class="ramka t-danger"><?= ($model->post_print_uf_lak ? 'Распаковка' : '') . ($model->post_print_uf_lak && $model->post_print_thermal_lift ? ' + ' : '') . ($model->post_print_thermal_lift ? 'Упаковка.' : '.') ?></div></td>
    </tr>
<?php endif; ?>
