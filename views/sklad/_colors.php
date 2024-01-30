<?php
/* @var $this yii\web\View */

use yii\web\View;

$chmkPos=0;
?>



    <div class="sklad-colors">
        <h4 id="color-max-date">Последние обновление: <span class="label label-primary"><?=Yii::$app->formatter->asDate($maxDate)?> - <?=Yii::$app->formatter->asTime($maxDate)?></span></h4>
        <table class="table">
            <tr>
                <?php foreach ($colorDate as $el):?>
                <th colspan="2"><?=$el['name']?></th>
                <th>Пр-во</th>
                <th>Склад</th>
                <th>Купить</th>
                <th>&nbsp;</th>

                <?php endforeach;?>
            </tr>
            <?php for($i=0;$i<$maxRow;$i++):?>
            <tr>
            <?php foreach ($colorDate as $el):?>
                <td class="color-name" style="background-color:#<?=$el['content'][$i]['colorRef']['color']?><?=hexdec($el['content'][$i]['colorRef']['color'])<13369456?';color:#fff;':''?>"><?=$el['content'][$i]['colorRef']['name']?></td>
                <td style="border-left: 1px solid;color:red;" class="color-art"><?=$el['content'][$i]['article']?></td>
                <td style="border-left: 1px solid" data-key="<?=$el['content'][$i]['id']?>" data-table="color" data-column-name="proizvodstvo"><?=$el['content'][$i]['proizvodstvo']?$el['content'][$i]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$el['content'][$i]['id']?>" data-table="color" data-column-name="sklad"><?=$el['content'][$i]['sklad']?$el['content'][$i]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$el['content'][$i]['id']?>" data-table="color" data-column-name="procurement"><?=$el['content'][$i]['procurement']?$el['content'][$i]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
            <?php endforeach;?>
            </tr>
            <?php endfor;?>
            <tr><td colspan="<?=count($colorDate)*6?>">&nbsp;</td></tr>
<!--            <tr>
                <?php foreach ($colorDate as $el):?>
                <th colspan="2"></th>
                <th>Пр-во</th>
                <th>Склад</th>
                <th>Купить</th>
                <th>&nbsp;</th>

                <?php endforeach;?>
            </tr>-->
            <tr>
                <th>Порошок</th>
                <td class="color-art">Золото</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <th>Химия:</th>
                <td class="color-art">SUN 64</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <th>Раствор.:</th>
                <td class="color-art">Турбо Б</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td></td>
                <td class="color-art">Серебро</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td></td>
                <td class="color-art">SVL 38</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="2">Ветошь (упаковка)</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
                <td></td>
                <td class="color-art">SJL 54</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="12">&nbsp;</td>
            </tr>
            <tr><td colspan="<?=count($colorDate)*6?>">&nbsp;</td></tr>
            <tr>
                <th>Трансфер:</th>
                <td class="color-art">Клей</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="2">Замедлитель 90911</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="12">&nbsp;</td>
            </tr>
            <tr>
                <td></td>
                <td class="color-art">Бумага</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="2">Адгезия 90908</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="12">&nbsp;</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="2">Клей спрей А9</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="12">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
                <td colspan="2">PoliGrip PP</td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="proizvodstvo" data-table="other"><?=$chemikal[$chmkPos]['proizvodstvo']?$chemikal[$chmkPos]['proizvodstvo']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="sklad" data-table="other"><?=$chemikal[$chmkPos]['sklad']?$chemikal[$chmkPos]['sklad']:''?></td>
                <td style="border-left: 1px solid" data-key="<?=$chemikal[$chmkPos]['id']?>" data-column-name="procurement" data-table="other"><?=$chemikal[$chmkPos]['procurement']?$chemikal[$chmkPos]['procurement']:''?></td>
                <td style="background-color:  #00cc00;">&nbsp;</td>
                <?php $chmkPos++;?>
                <td colspan="12">&nbsp;</td>
            </tr>

        </table>
    </div>