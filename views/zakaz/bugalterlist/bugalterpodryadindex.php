<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Бухгалтерия подрядчики';
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
        requestUrl: "<?= Url::to( ['/zakaz/bugalterlist/listbugalterpodryad'] ) ?>",
        setsizesUrl: "<?= Url::to( ['/zakaz/bugalterlist/setsizesmbugalterpodryad'] ) ?>",
        getAvailableColumnsUrl: "<?= Url::to( ['/zakaz/bugalterlist/getavailablecolumnsbugalterpodryad'] ) ?>",
        setColumnsUrl: "<?= Url::to( ['/zakaz/bugalterlist/setcolumnsbugalterpodryad'] ) ?>",
        userId: "<?= Yii::$app->user->identity->id ?>",
//        zakazRequestURL:"<?= Url::to( ['/zakaz/zakaz/getstored'] ) ?>",
        viewRowUrl: "<?= (bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/view' ) ? Url::to( [
            '/zakaz/zakazlist/view'] ) : null ?>",
        savePaiedUrl: "<?= (bool) \Yii::$app->user->identity->can( 'zakaz/bugalterlist/paiedbugalterpodryad' ) ? Url::to( [
            '/zakaz/bugalterlist/paiedbugalterpodryad'] ) : null ?>",
    };
    $('#zakaz-listB').bugalterPostZakazController(options);

</script>
<?php JSRegister::end(); ?>
<div class="zakaz-list-cont">
    <div class="zakaz-list-table">
        <table id="zakaz-listB" class="table table-bordered dirt">
            <caption>
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

            </caption>
            <tbody></tbody>
        </table>
    </div>
</div>