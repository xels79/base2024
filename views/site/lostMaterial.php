<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $model array
 */

use yii\widgets\ListView;
use yii\helpers\Html;
use \yii\helpers\Url;

$this->title = $title;
?>
<div class="container-fluid">
    <p><?= Html::a('Просмотреть логи', Url::to(['logs'])) ?></p>
    <p><?= $infoString ?> <span style="color:<?= $loasMaterialCount ? 'red' : 'white' ?>"><?= $loasMaterialCount ?></p>
    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView'     => '_lostMaterial',
        'layout'       => '{summary}<div class="panel-group" id="accordion_main" role="tablist" aria-multiselectable="true">{items}</div>{pager}',
        'itemOptions'  => [
            'class' => 'panel panel-primary'
        ]
    ])
    ?>
</div>