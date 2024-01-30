<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $this->context->title.'/Менеджеры';
$this->params['breadcrumbs'][] = 'Менеджеры';
?>
<div class="tbl-user-index">

    <h1><?= Html::encode('Менеджеры') ?></h1>

    <p>
        <?= \Yii::$app->user->can('create')?Html::a('Добавить менеджера', ['create'], ['class' => 'btn btn-success']):'' ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'Показаны с {begin} по {end} Всего {totalCount}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'realname',
            'username',
            'email:email',
            'utypeRus',
            ['class' => 'app\widgets\MActionColumn',
                'template'=>'{view} {update} {delete}',
                'modelKeyToConfirm'=>'realname',
                'confirm'=>'Удалить менеджера "{info}" ?'                
            ],
        ],
    ]); ?>

</div>
