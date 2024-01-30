<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
?>

<?php    Pjax::begin([
    'id'=>'customerListPjax'
]);?>
    <div class="dialog-list customer-list">
        <div class="dialog-row">
            <?=(Yii::$app->user->identity->can('admin/mainpage/ajaxlist_zakone')?(Html::tag('div',Html::button('Добавить', ['id'=>'addFirm','class' => 'btn btn-main btn-xs','role'=>'add']),['class'=>'dialog-row'])): '')?>
        </div>
        <div class="dialog-row">

            <?=GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions'=>['class'=>'table'],
                'summary'=>false,//'Показаны с {begin} по {end} Всего {totalCount}',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'mainName',
                    'status'=>[
                        'attribute'=>'status',
                        'value'=>function ($model){
                            return $model->status?'Активен':'нет';
                        },
                        'format'=>'text',
                    ]
                ],
                'rowOptions'=>function ($model, $key, $index, $grid){
                    $rVal=['data'=>[
                        'key'=>$model->firm_id,
                        'ismain'=>true
                    ]];
                    if  ($model->status){
                        $rVal['class']='bg-success';
                    } else {
                        $rVal['class']='bg-danger';
                    }
                    return $rVal;
                }
            ])?>
        </div>
    </div>
<?php Pjax::end();?>
