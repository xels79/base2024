<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use app\controllers\zakaz\ZakazProizvodstvo;

$this->title = 'Заказы дизайнера';
$this->renderFile( '@app/views/layouts/zakaz-wrap.php' );
?>
<?php
JSRegister::begin( [
    'key'      => 'ZakazListInit',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>
    var options = {
        bigLoaderPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader.gif' )[1] ?>",
        requestUrl: "<?= Url::to( ['/zakaz/zakazlist/listdisainer'] ) ?>",
        setsizesUrl: "<?= Url::to( ['/zakaz/zakazlist/setsizesmdisainer'] ) ?>",
        getAvailableColumnsUrl: "<?= Url::to( ['/zakaz/zakazlist/getavailablecolumnsdisainer'] ) ?>",
        setColumnsUrl: "<?= Url::to( ['/zakaz/zakazlist/setcolumnsdisainer'] ) ?>",
        userId: "<?= Yii::$app->user->identity->id ?>",
        zakazRequestURL: "<?= Url::to( ['/zakaz/zakaz/getstored'] ) ?>",
        viewRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/view' ) ? Url::to( [
            '/zakaz/zakazlist/view'] ) : null
?>",
        changeRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/stagedisainer' ) ? Url::to( [
            '/zakaz/zakazlist/stagedisainer'] ) : null
?>",
        isProizvodstvo: false,
        isAdmin:<?= Yii::$app->user->identity->role === 'admin' ? 'true' : 'false' ?>,
//        zakazDirtRemoveURL:"<?= Url::to( ['/zakaz/zakaz/removezakaz'] ) ?>"
    };
    $('#zakaz-list').disainerZakazController(options);

</script>
<?php JSRegister::end(); ?>
<div class="zakaz-list-cont">
    <!--<div class="header"><h3><?= $this->title ?></h3></div>-->
    <div class="zakaz-list-table">
        <div id="zakaz-list" class="resize-table dirt">
            <div class="resize-caption"><div class="table-header"><?= $this->title ?>.</div><div><small><em class="text-muted">Двойной клик чтобы открыть или правая кнопка операции с заказом</em></small></div></div>
            <div class="resize-header"></div>
            <div class="resize-tbody"></div>
            <div class="resize-tfooter"></div>

        </div>
    </div>
</div>