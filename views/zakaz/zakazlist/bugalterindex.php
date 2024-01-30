<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;
$this->title='Бугалтерия';
$this->renderFile('@app/views/layouts/zakaz-wrap.php');
?>
<?php JSRegister::begin([
    'key' => 'ZakazListInit',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    var options={
        bigLoaderPicUrl:"<?=Yii::$app->assetManager->publish('@app/web/pic/loader.gif')[1]?>",
        requestUrl:"<?=Url::to(['/zakaz/zakazlist/listbugalter'])?>",
        setsizesUrl:"<?=Url::to(['/zakaz/zakazlist/setsizesmbugalter'])?>",
        getAvailableColumnsUrl:"<?=Url::to(['/zakaz/zakazlist/getavailablecolumnsbugalter'])?>",
        setColumnsUrl:"<?=Url::to(['/zakaz/zakazlist/setcolumnsbugalter'])?>",
        userId:"<?=Yii::$app->user->identity->id?>",
        zakazRequestURL:"<?=Url::to(['/zakaz/zakaz/getstored'])?>",
        viewRowUrl:"<?=(bool)\Yii::$app->user->identity->can('/zakaz/zakazlist/view')?Url::to(['/zakaz/zakazlist/view']):null?>",
        changeRowUrl:"<?=(bool)\Yii::$app->user->identity->can('zakaz/zakazlist/stagebugalter')?Url::to(['/zakaz/zakazlist/stagebugalter']):null?>",
        canChange:[4,5,6],
        canChangeTo:[3,5,6,8],

//        zakazDirtRemoveURL:"<?=Url::to(['/zakaz/zakaz/removezakaz'])?>"
    };
    $('#zakaz-list').bugalterZakazController(options);

</script>
<?php JSRegister::end();?>
<div class="zakaz-list-cont">
    <div class="header"><h3>Не работает пока. Но контроллер есть..wsss.</h3></div>
    <div class="zakaz-list-table">
        <table id="zakaz-list" class="table table-bordered">
            <caption><h3><?=$this->title?>.</h3><small><em class="text-muted">Двойной клик чтобы открыть или правая кнопка операции с заказом</em></small></caption>
            <tbody></tbody>
        </table>
    </div>
</div>