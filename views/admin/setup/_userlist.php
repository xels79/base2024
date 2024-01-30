<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<?php    yii\widgets\Pjax::begin([
    'id'=>'userlistPjax',
]);?>

<?=GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions'=>['class'=>'table'],
                    'summary'=>false,//'Показаны с {begin} по {end} Всего {totalCount}',
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'realname',
                        'username',
                        'email:email',
                        'utypeRus',
                    ],
                ])?>
<?php yii\widgets\Pjax::end();?>