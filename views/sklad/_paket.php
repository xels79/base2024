<?php
/* @var $this yii\web\View */

use yii\web\View;
use app\widgets\JSRegister;
use yii\helpers\Url;
$i=0;
?>
<div class="sklad-paket">
    <h4 id="paket-max-date">Последние обновление: <span class="label label-primary"><?=Yii::$app->formatter->asDate($maxDate)?> - <?=Yii::$app->formatter->asTime($maxDate)?></span></h4>
    <table class="table">
        <?php foreach ($paketFirmAll as $firm):?>
        <tr>
            <th><?=$firm['name']?></th>
            <?php foreach($colls as $el):?>
                <th colspan="3"><?=$el?></th>
            <?php endforeach;?>
        </tr>
            <th>&nbsp;</th>
            <?php foreach($colls as $el):?>
            <th>Цена</th>
            <th>Склад</th>
            <th>Резерв</th>
            <?php endforeach;?>
        <?php foreach($firm['skladPakets'] as $paket):?>
        <tr>
            <td class="color-name" style="background-color: #<?=$paket['colorRef']['color']?><?=hexdec($paket['colorRef']['color'])<13369456?';color:#fff;':''?>"><?=$paket['colorRef']['name']?></td>
            <?php foreach($colls as $col):?>
                <td style="border-left: 1px solid" data-column-name="<?='coast_sz_'.$col?>" data-key="<?=$paket['id']?>" data-table="paket"><?=$paket['coast_sz_'.$col]?$paket['coast_sz_'.$col]:''?></td>
                <td style="border-left: 1px solid" data-column-name="<?='sklad_sz_'.$col?>" data-key="<?=$paket['id']?>" data-table="paket"><?=$paket['sklad_sz_'.$col]?$paket['sklad_sz_'.$col]:''?></td>
                <td style="border-left: 1px solid" data-column-name="<?='rezerv_sz_'.$col?>" data-key="<?=$paket['id']?>" data-table="paket"><?=$paket['rezerv_sz_'.$col]?$paket['rezerv_sz_'.$col]:''?></td>
            <?php endforeach;?>
        </tr>
        <?php endforeach;?>        
        <?php        endforeach;?>
    </table>
</div>