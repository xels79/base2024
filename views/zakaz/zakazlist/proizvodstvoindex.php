<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;
use app\controllers\zakaz\ZakazProizvodstvo;
use yii\helpers\Json;

$this->title = 'Заказы для производства';
$this->renderFile( '@app/views/layouts/zakaz-wrap.php' );
$this->registerJsFile( 'js/tinymce/tinymce.min.js', ['position' => \yii\web\View::POS_END] );
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
        requestUrl: "<?= Url::to( ['/zakaz/zakazlist/listproizvodstvo'] ) ?>",
        setsizesUrl: "<?= Url::to( ['/zakaz/zakazlist/setsizesmproizvodstvo'] ) ?>",
        getAvailableColumnsUrl: "<?= Url::to( ['/zakaz/zakazlist/getavailablecolumnsproizvodstvo'] ) ?>",
        setColumnsUrl: "<?= Url::to( ['/zakaz/zakazlist/setcolumnsproizvodstvo'] ) ?>",
        userId: "<?= Yii::$app->user->identity->id ?>",
        zakazRequestURL: "<?= Url::to( ['/zakaz/zakaz/getstored'] ) ?>",
        viewRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/view' ) ? Url::to( [
            '/zakaz/zakazlist/view'] ) : null
?>",
        changeRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/stageproizvodstvo' ) ? Url::to( [
            '/zakaz/zakazlist/stageproizvodstvo'] ) : null
?>",
        petchatniAddUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/addpechatnik' ) ? Url::to( [
            '/zakaz/zakazlist/addpechatnik'] ) : null
?>",
        petchatnikRemoveUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/removepechatnik' ) ? Url::to( [
            '/zakaz/zakazlist/removepechatnik'] ) : null
?>",
        petchatnikToTomorowUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/totomorowpechatnik' ) ? Url::to( [
            '/zakaz/zakazlist/totomorowpechatnik'] ) : null
?>",
        petchatnikReadyUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/toreadypechatnik' ) ? Url::to( [
            '/zakaz/zakazlist/toreadypechatnik'] ) : null
?>",
        canChange: [4, 5, 6],
        canChangeTo:<?= Json::encode( ZakazProizvodstvo::$proizvodstvoCanChangeTo ) ?>,
        isProizvodstvo: true,
        isAdmin:<?= Yii::$app->user->identity->role === 'admin' ? 'true' : 'false' ?>

//        zakazDirtRemoveURL:"<?= Url::to( ['/zakaz/zakaz/removezakaz'] ) ?>"
    };
    if ($('#zametki').length) {
        $('#zametki').zametkiOpen({
            listUrl: '<?=
Yii::$app->user->identity->can( '/zametki/list' ) ? Url::to( [
            '/zametki/list'] ) : ''
?>',
            saveFileUrl: '<?=
Yii::$app->user->identity->can( '/zametki/save' ) ? Url::to( [
            '/zametki/save'] ) : ''
?>',
            removeUrl: '<?=
Yii::$app->user->identity->can( '/zametki/remove' ) ? Url::to( [
            '/zametki/remove'] ) : ''
?>',
            getfileUrl: '<?=
Yii::$app->user->identity->can( '/zametki/getfile' ) ? Url::to( [
            '/zametki/getfile'] ) : ''
?>',
            //sendUrl: '<?= Url::to( ['/rekvizit/send'] ) ?>',
            addTabUrl: '<?=
Yii::$app->user->identity->can( '/zametki/addtab' ) ? Url::to( [
            '/zametki/addtab'] ) : ''
?>',
            removeTabUrl: '<?=
Yii::$app->user->identity->can( '/zametki/removetab' ) ? Url::to( [
            '/zametki/removetab'] ) : ''
?>',
            renameTabUrl: '<?=
Yii::$app->user->identity->can( '/zametki/renametab' ) ? Url::to( [
            '/zametki/renametab'] ) : ''
?>',
            renameZametkaUrl: '<?=
Yii::$app->user->identity->can( '/zametki/renametab' ) ? Url::to( [
            '/zametki/renametab'] ) : ''
?>',
            uploadMCIUrl: '<?=
Yii::$app->user->identity->can( '/mceimage/save' ) ? Url::to( [
            '/mceimage/save'] ) : ''
?>',
            fNamePerfix: 'zametki',
            title: 'Заметки',
        });
    }
    $('#zakaz-list').disainerZakazController(options);

</script>
<?php JSRegister::end(); ?>
<div class="zakaz-list-cont hidden-print">
    <?php if ( Yii::$app->user->identity->role == "proizvodstvo" || Yii::$app->user->identity->role == "proizvodstvochief" ): ?><div class="container-fluid" style="padding-bottom: 10px;"><?=
        Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material-zametki main-button',
                    'style' => ['width' => '150px']] ), '#', [
            'id' => 'zametki'] )
        ?></div><?php endif; ?>
    <div class="zakaz-list-table">
        <div id="zakaz-list" class="resize-table">
            <div class="resize-caption"><h3><?= $this->title ?>.</h3><small><em class="text-muted">Двойной клик чтобы открыть или правая кнопка операции с заказом</em></small></div>
            <div class="resize-header"></div>
            <div class="resize-tbody"></div>
            <div class="resize-tfooter"></div>
        </div>
    </div>
</div>
<div class="petchatnik-list-cont">
    <div class="cont" id="p_cont"></div>
</div>