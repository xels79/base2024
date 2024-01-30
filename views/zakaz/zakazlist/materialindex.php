<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author Александр
 */
use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $isProduct ? 'Материалы для производства' : 'Список не заказанных материалов';
$this->renderFile( '@app/views/layouts/zakaz-wrap.php' );
?>
<?php
JSRegister::begin( [
    'key'      => 'ZakazMaterialListInit',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>
    var options = {
        bigLoaderPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader.gif' )[1] ?>",
        requestUrl: "<?=
$isProduct ? Url::to( ['/zakaz/zakazlist/listmaterialp',
            'isProduct' => 1] ) : Url::to( ['/zakaz/zakazlist/listmaterialp'] )
?>",
        setsizesUrl: "<?=
$isProduct ? Url::to( ['/zakaz/zakazlist/setsizesmp',
            'isProduct' => 1] ) : Url::to( ['/zakaz/zakazlist/setsizesmp'] )
?>",
        getAvailableColumnsUrl: "<?=
$isProduct ? Url::to( ['/zakaz/zakazlist/getavailablecolumnsmaterialsp',
            'isProduct' => 1] ) : Url::to( ['/zakaz/zakazlist/getavailablecolumnsmaterialsp'] )
?>",
        setColumnsUrl: "<?=
$isProduct ? Url::to( ['/zakaz/zakazlist/setcolumnsmp',
            'isProduct' => 1] ) : Url::to( ['/zakaz/zakazlist/setcolumnsmp'] )
?>",
        changeMaterialStateUrl: "<?= Url::to( ['/zakaz/zakazlist/changematerialstate'] ) ?>",
        canEditOtherOrder: "<?= (bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/editanyorder' ) ?>",
        userId: "<?= \Yii::$app->user->identity->id ?>",
        isProduction: "<?= $isProduct ?>",
        changeRowUrl: "<?=
$isProduct && Yii::$app->user->identity->can( '/zakaz/zakazlist/stageproizvodsvo' ) ? Url::to( [
            '/zakaz/zakazlist/stageproizvodsvo', 'isProduct' => 1] ) : ""
?>"
    };
    $('#zakaz-listMater').materialToOrder(options);

</script>
<?php JSRegister::end(); ?>
<div class="zakaz-list-cont">
    <!--<div class="header"><h2><code>//Не балуйся пока плиз...</code></h2></div>-->
    <div class="zakaz-list-table">
        <div id="zakaz-listMater" class="resize-table">
            <div class="resize-caption"><div class="table-header"><?= $this->title ?></div></div>
            <div class="resize-header"></div>
            <div class="resize-tbody"></div>
            <div class="resize-tfooter"></div>
        </div>
    </div>
</div>