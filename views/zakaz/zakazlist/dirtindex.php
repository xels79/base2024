<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;
$this->title='Черновики';
$this->renderFile('@app/views/layouts/zakaz-wrap.php');
?>
<?php JSRegister::begin([
    'key' => 'ZakazListInit',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    var options={
        bigLoaderPicUrl:"<?=Yii::$app->assetManager->publish('@app/web/pic/loader.gif')[1]?>",
        requestUrl:"<?=Url::to(['/zakaz/zakazlist/listdirt'])?>",
        setsizesUrl:"<?=Url::to(['/zakaz/zakazlist/setsizesdirt'])?>",
        getAvailableColumnsUrl:"<?=Url::to(['/zakaz/zakazlist/getavailablecolumnsdirt'])?>",
        setColumnsUrl:"<?=Url::to(['/zakaz/zakazlist/setcolumnsdirt'])?>",
        userId:"<?=Yii::$app->user->identity->id?>",
        zakazRequestURL:"<?=Url::to(['/zakaz/zakaz/getstored'])?>",
        zakazDirtRemoveURL:"<?=Url::to(['/zakaz/zakaz/removezakaz'])?>"
    };
    $('#zakaz-list').dirtZakazController(options);

</script>
<?php JSRegister::end();?>
<div class="zakaz-list-cont">
    <div class="header"><h3><?=$this->title?></h3></div>
    <div class="zakaz-list-table">
        <table id="zakaz-list" class="table table-bordered dirt">
            <caption><h3><?=$this->title?></h3><small><em class="text-muted">Двойной клик чтобы открыть.</em></small></caption>
            <tbody></tbody>
        </table>
    </div>
</div>