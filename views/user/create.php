<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TblUser */

$this->title = $this->context->title.'/Добавиь мэнеджера';
$this->params['breadcrumbs'][] = 'Добавиь нового мэнеджера';
?>
<div class="tbl-user-create">

    <h1><?= Html::encode('Добавить нового менеджера') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
