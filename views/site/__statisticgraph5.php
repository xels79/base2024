<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $this yii\web\View
 * @var $yaer integer Текущий год
 * @var $month string Номер месяца с ведущим нулём
 * @var $actions array Параметры экшенов
 */
use Yii;
use app\widgets\SpendingTable\SpendingTable;
use app\widgets\SpendingTable\StaticSpendingTable;

use app\widgets\SpendingTable\MainSpendingTable;
use yii\helpers\Url;
$date="$yaer-$month-01";
?>
<div>
    <?=MainSpendingTable::widget([
        'date'=>$date,
        'action'=>$actions['MainSpendingTable']['action'],
        'jsVarName'=>"conectVar_$date_$month"
    ])?>
    
    <?=StaticSpendingTable::widget([
        'date'=>$date,
    ])?>
    <?=SpendingTable::widget([
        'modelName'=>"app\models\Spends",
        'date'=>$date,
        'columnsTypes'=>['coast'=>'currency'],
        'action'=>$actions['SpendingTable']['action'],
        'actionAdd'=>$actions['SpendingTable']['actionAdd'],
        'actionRemove'=>$actions['SpendingTable']['actionRemove'],
        'options'=>[
            'style'=>'width:500px;'
        ],
        'jsVarName'=>"conectVar_$date_$month"
    ])?>
</div>
