<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = 'Ведутся работы';
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="reglament">
        <?= Html::img('@web/pic/blocked.jpg') ?>
        <?= Html::img('@web/pic/blocked2.png',['height'=>700]) ?>
        </div>
    </div>

</div>
