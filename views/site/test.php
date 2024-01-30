<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\widgets\JSRegister;
use app\models\tables\DependTables;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
app\mainasset\materials\MaterialsAsset::register($this);
app\mainasset\materials2\Materilas2Asset::register($this);
?>
<?php JSRegister::begin([
    'key' => 'site_test',
    'position' => \yii\web\View::POS_READY
]); ?>

<script>
    $('#requestMaterials').click(function ( ) {
        new stones.materials.select.eng({
            loadPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader-hor.gif' )[1] ?>",
            materialRequestListUrl: "<?=Yii::$app->urlManager->createUrl( ['mselect/list'] )?>",
         });
    });
    $('#requestMaterials2').click(function ( ) {
        new stones.materials2.select.eng({
            loadPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader-hor.gif' )[1] ?>",
            materialRequestListUrl: "<?=Yii::$app->urlManager->createUrl( ['mselect/list'] )?>",
         });
    });
</script>
<?php JSRegister::end();?>
<div class="zakaz-list-cont">
    <div><?=$cont?></div>
    <ul class="a-list">
        <li><?=
            Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material main-button'] ), '#', [
                'id' => 'requestMaterials'] )
            ?></li>
        <li><?=
            Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material main-button'] ), '#', [
                'id' => 'requestMaterials2'] )
            ?></li>
    </ul>
</div>