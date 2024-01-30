<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TblUser */

$this->title =$this->context->title.'/Мэнеджер: '.$model->realname;
$this->params['breadcrumbs'][] = 'Мэнеджер: '.$model->realname;
?>
<div class="tbl-user-view">

    <h1><?= Html::encode('Мэнеджер "'.$model->realname.'" ') ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php 
            if ($this->context->role=='admin')
                echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены что хотите удалить мэнеджера '.$model->realname.'?',
                        'method' => 'post',
                    ],
                ]);
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'realname',
            'username',
            'email:email',
            'utypeRus',
        ],
    ]) ?>

</div>
