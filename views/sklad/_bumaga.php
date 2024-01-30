<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="sklad-bumaga">
    <h4 id="bumaga-max-date">Последние обновление: <span class="label label-primary"><?=Yii::$app->formatter->asDate($maxDate)?> - <?=Yii::$app->formatter->asTime($maxDate)?></span></h4>
    <table class="table">
        <tr>
            <th colspan="2">&nbsp;</th>
            <th>Склад</th>
            <th>Резерв</th>
        </tr>
        <?php foreach ($bumaga as $b):?>
        <tr>
            <th><?=$b['name']?></th>
            <td class="color-name"<?=$b['content'][0]['colorRef']['color']!=='#'?(' style="background-color:#'.$b['content'][0]['colorRef']['color'].';'.(hexdec($b['content'][0]['colorRef']['color'])<11725505?' color:#fff;':'').'"'):''?>><?=$b['content'][0]['colorRef']['name']?></td>
            <td data-column-name="sklad" data-key="<?=$b['content'][0]['id']?>" data-table="bumaga"><?=$b['content'][0]['sklad']?$b['content'][0]['sklad']:''?></td>
            <td data-column-name="rezerv" data-key="<?=$b['content'][0]['id']?>" data-table="bumaga"><?=$b['content'][0]['rezerv']?$b['content'][0]['rezerv']:''?></td>
        </tr>
        <?php for($i=1;$i<count($b['content']);$i++):?>
        <tr>
            <th>&nbsp;</th>
            <td class="color-name"<?=$b['content'][$i]['colorRef']['color']!=='#'?(' style="background-color:#'.$b['content'][$i]['colorRef']['color'].';'.(hexdec($b['content'][$i]['colorRef']['color'])<11725505?' color:#fff;':'').'"'):''?>><?=$b['content'][$i]['colorRef']['name']?></td>
            <td data-column-name="sklad" data-key="<?=$b['content'][$i]['id']?>" data-table="bumaga"><?=$b['content'][$i]['sklad']?$b['content'][$i]['sklad']:''?></td>
            <td data-column-name="rezerv" data-key="<?=$b['content'][$i]['id']?>" data-table="bumaga"><?=$b['content'][$i]['rezerv']?$b['content'][$i]['rezerv']:''?></td>
        </tr>
        <?php endfor;?>
        <?php endforeach;?>
    </table>
</div>
