<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 *
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 */

use yii\widgets\ListView;
use yii\helpers\Url;

$this->title = 'Логи сохранения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <p><a href="<?= Url::to(['checkmaterials']) ?>">Проверить материалы</a></p>
    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView'     => '_logs',
        'layout'       => '{summary}<div class="panel-group" id="accordion_main" role="tablist" aria-multiselectable="true">{items}</div>{pager}',
        'itemOptions'  => [
            'class' => 'panel panel-primary'
        ],
        'pager'        => [
            'firstPageLabel' => 'Начало',
            'lastPageLabel'  => 'Конец'
        ]
    ])
    ?>
    <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#myModal">
        Очистить логи
    </button>

    <!-- Модаль -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Внимание</h4>
                </div>
                <div class="modal-body">
                    Очистить лог файл?
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-default" data-dismiss="modal">Отменить</a>
                    <a type="button" class="btn btn-danger" href="<?= Url::to(['logs', 'flush' => true]) ?>">Очистить логи</a>
                </div>
            </div>
        </div>
    </div></div>