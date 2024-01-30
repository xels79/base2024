<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Бухгалтерия поставщики';
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
        requestUrl: "<?= Url::to( ['/zakaz/bugalterlist/listbugalterpost'] ) ?>",
        setsizesUrl: "<?= Url::to( ['/zakaz/bugalterlist/setsizesmbugalterpost'] ) ?>",
        getAvailableColumnsUrl: "<?= Url::to( ['/zakaz/bugalterlist/getavailablecolumnsbugalterpost'] ) ?>",
        setColumnsUrl: "<?= Url::to( ['/zakaz/bugalterlist/setcolumnsbugalterpost'] ) ?>",
        userId: "<?= Yii::$app->user->identity->id ?>",
        zakazRequestURL: "<?= Url::to( ['/zakaz/zakaz/getstored'] ) ?>",
        viewRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/view' ) ? Url::to( [
            '/zakaz/zakazlist/view'] ) : null
?>",
        savePaiedUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/bugalterlist/paiedbugalterpost' ) ? Url::to( [
            '/zakaz/bugalterlist/paiedbugalterpost'] ) : null
?>",
//        zakazDirtRemoveURL:"<?= Url::to( ['/zakaz/zakaz/removezakaz'] ) ?>"
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
        </table>
    </div>
</div>