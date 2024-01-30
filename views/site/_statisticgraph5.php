<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $this yii\web\View
 */
use yii;
use app\widgets\SpendingTableTabs\SpendingTableTabs;
use app\widgets\SpendingToYourself\SpendingToYourself;
use yii\helpers\Url;
?>
<div>
    <h1 style="padding-top: 30px;">Траты:</h1>
    <?=SpendingTableTabs::widget([
        'viewFileName'=>'__statisticgraph5',
        'actions'=>[
            'SpendingTable'=>[
                'action'=>Url::to(['/site/statisticgraph','actionSpend'=>'saveSpend']),
                'actionAdd'=>Url::to(['/site/statisticgraph','actionSpend'=>'saveSpend']),
                'actionRemove'=>Url::to(['/site/statisticgraph','actionSpend'=>'removeSpend'])
            ],
            'MainSpendingTable'=>[
                'action'=>Url::to(['/site/statisticgraph','actionSpend'=>'saveMainSpend']),
            ]
        ]
    ])?>
    
</div>
