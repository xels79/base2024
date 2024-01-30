<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $title;
$this->renderFile( '@app/views/layouts/zakaz-wrap.php' );
?>
<?php
JSRegister::begin( [
    'key'      => 'ZakazListInit',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>
    let options = {
        bigLoaderPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/preloader.gif' )[1] ?>",
        requestUrl: "<?= Url::to( ["/$controllerId/listbugalter"] ) ?>",
        setsizesUrl: "<?= Url::to( ["/$controllerId/setsizesmbugalter"] ) ?>",
        getAvailableColumnsUrl: "<?= Url::to( ["/$controllerId/getavailablecolumnsbugalter"] ) ?>",
        setColumnsUrl: "<?= Url::to( ["/$controllerId/setcolumnsbugalter"] ) ?>",
        userId: "<?= Yii::$app->user->identity->id ?>",
        zakazRequestURL: "<?= Url::to( ['/zakaz/zakaz/getstored'] ) ?>",
        viewRowUrl: "<?= (bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/view' ) ? Url::to( [
            '/zakaz/zakazlist/view'] ) : null ?>",
        changeRowUrl: "<?= (bool) \Yii::$app->user->identity->can( "$controllerId/stagebugalter" ) ? Url::to( [
            "/$controllerId/stagebugalter"] ) : null ?>",
        urlGetOplataList: "<?= Url::to( ["/$controllerId/getoplatalist"] ) ?>",
        urlUpdateOplataList: "<?= Url::to( ["/$controllerId/updateoplatalist"] ) ?>",
        urlGetOneRow: "<?= Url::to( ["/$controllerId/getonerow"] ) ?>",
        urlSetMaterialStatus: "<?= Url::to( ["/$controllerId/setmaterialstatus"] ) ?>",
        canChange: [4, 5, 6],
        canChangeTo: [3, 5, 6, 8],

//        zakazDirtRemoveURL:"<?= Url::to( ['/zakaz/zakaz/removezakaz'] ) ?>"
    };
    $('#zakaz-listB').bugalterZakazController(options);

</script>
<?php JSRegister::end(); ?>
<div class="zakaz-list-cont">
    <!--<div class="header"><h2><code>//Не балуйся пока плиз...</code></h2></div>-->
    <div class="zakaz-list-table">
        <div id="zakaz-listB" class="resize-table">
            <div class="resize-caption"><div class="table-header">
                <h3><?= $this->title ?>.</h3>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">с</span>
                    <input type="text" class="form-control" placeholder="дд.мм.гггг" aria-describedby="basic-addon1" id="date-from">
                </div>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon2">по</span>
                    <input type="text" class="form-control" placeholder="дд.мм.гггг" aria-describedby="basic-addon2" id="date-to">
                </div>
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default" title="Сбросить временные рамки" id="time-reset">Сбросить</button>
                    </div>
                </div>
            </div></div>
            <div class="resize-header"></div>
            <div class="resize-tbody"></div>
            <div class="resize-tfooter"></div>
        </div>
    </div>
</div>