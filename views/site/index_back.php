<?php
/* @var $this yii\web\View */
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\AList;

$this->title = 'Астерион - база';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Астерион база</h1>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <h2>Базовые таблицы</h2>

                <p><b>Базовая информация</b> для работы содержиться в таблицах:</p>

                <p><?= Nav::widget([
                    'options'=>['class'=>['nav-pills','nav-stacked','list-group']],
                    'encodeLabels'=>false,
                    'items'=>[
                       // '<li class="dropdown-header">Dropdown Header</li>',
                        [
                            'label'=>'<h4>Название фирм</h4><p>информация о фирмах и их сотрудниках.</p>',
                            'url'=>['admin/firms'],
                            //'linkOptions'=>['class'=>'']
                        ],
                        [
                            'label'=>'<h4>Контактное лицо и Ответственный</h4><p>информация о сотрудниках сторонних фирм.</p>',
                            'url'=>['admin/manager']
                        ],
                        [
                            'label'=>'<h4>Способ печати</h4><p>названия методов исполнения заказов.</p>',
                            'url'=>['admin/metodispolnenya']
                        ],
                        [
                            'label'=>'<h4>Продукция</h4><p>названия типов заказов</p>' ,
                            'url'=>['admin/nazvaniezakaza']
                        ],
                        [
                            'label'=>'<h4>Материалы</h4><p> параметры различных материалов</p>' ,
                            'url'=>['/zakazi/material']
                        ],
                    ]
                ])?></p>
            </div>
            <div class="col-lg-6 col-sm-6">
                <h2>Заказы</h2>
                <p><?=AList::widget([
                    'items'=>[
                        ['label'=>'<b>Все</b><span class="badge">'.$countList['all'].'</span>','url'=>['zakazi/zakaz']],
                        ['label'=>'<b>Согласование</b>','badge'=>$countList['soglasovanye'],'badgeOptions'=>['class'=>'badgePrimary'],'url'=>[
                                'zakazi/zakaz',
                                'ZakazSearch'=>[
                                    'searchStageId'=>0,
                                    'searchManagerId'=>'',
                                    'searchFirm'=>'',
                                    'searchOrderType'=>'',
                                    'searchPayment'=>'',
                                    'searchAdmission'=>'',
                                    'managerId'=>''
                                ]
                            ]
                        ],
                        ['label'=>'<b>У дизайнера</b>','badge'=>$countList['disayn'],'badgeOptions'=>['class'=>'badgeAlert'],'url'=>[
                                'zakazi/zakaz',
                                'ZakazSearch'=>[
                                    'searchStageId'=>1,
                                    'searchManagerId'=>'',
                                    'searchFirm'=>'',
                                    'searchOrderType'=>'',
                                    'searchPayment'=>'',
                                    'searchAdmission'=>'',
                                    'managerId'=>''
                                ]
                            ]
                        ],
                        ['label'=>'<b>Печать</b>','badge'=>$countList['print'],'badgeOptions'=>['class'=>'badgeWarning'],'url'=>[
                                'zakazi/zakaz',
                                'ZakazSearch'=>[
                                    'searchStageId'=>2,
                                    'searchManagerId'=>'',
                                    'searchFirm'=>'',
                                    'searchOrderType'=>'',
                                    'searchPayment'=>'',
                                    'searchAdmission'=>'',
                                    'managerId'=>''
                                ]
                            ]
                        ],
                        ['label'=>'<b>Оплаченные</b>','badge'=>$countList['handed'],'badgeOptions'=>['class'=>'badgeOk'],'url'=>[
                                'zakazi/zakaz',
                                'ZakazSearch'=>[
                                    'searchStageId'=>3,
                                    'searchManagerId'=>'',
                                    'searchFirm'=>'',
                                    'searchOrderType'=>'',
                                    'searchPayment'=>'',
                                    'searchAdmission'=>'',
                                    'managerId'=>''
                                ]
                            ]
                        ],
                        '<b>Поиск по дате</b>'
                    ],
                    'options'=>['class'=>'list-group'],
                    'itemOptions'=>['class'=>'list-group-item']
                ])?></p>
            </div>
        </div>
    </div>
</div>