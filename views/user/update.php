<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TblUser */

$this->title = $this->context->title.'/Изменить профиль мэнеджера : '. $model->realname;
$this->params['breadcrumbs'][] = ['label' => $model->realname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="tbl-user-update">

    <h1><?= Html::encode('Изменить профиль мэнеджера : ' . $model->realname) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
