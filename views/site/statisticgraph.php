<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * @var $this yii\web\View
 * @var $managers_exists array
 * $var $managers array
 * @var $periud int

 */

use yii\bootstrap\Tabs;
use app\widgets\JSRegister;
use yii\helpers\Json;
use app\mainasset\statisticgraph\StatisticAsset;


StatisticAsset::register($this);
$activeTab=(int)Yii::$app->request->get('graphicsMainTabsTab',0);
$this->title = 'Статистика график';
?>
<div class="statistic" id="statistic-cont">
<?=Tabs::widget([
    'id'=>'graphicsMainTabs',
    'items' => [
        [
            'label' => 'Статистика по менеджеру',
            'content' => $this->render('_statisticgraph1', [
                'test'            => $test,
                'periud'          => $periud,
                'managers'        => $managers,
                'managers_exists' => $managers_exists,
                //'total'           => $total,
//                'selectedYear'    => $selectedYear,
//                'gModel'          => $gModel
            ]),
            'active' => $activeTab===0
        ],
        [
            'label' => 'Графики общие',
            'content' => $this->render('_statisticgraph2',[
                'gModel'          => $gModel,
                'managers_exists' => $managers_exists,
                'total'           => $total,
                'selectedYear'    => $selectedYear,
            ]),
            'active' => $activeTab===1
        ],
        [
            'label' => 'Сводный график',
            'content' => $this->render('_statisticgraph3', ['gModel2'=>$gModel2, 'crossGraph'=>$crossGraph]),
            'active' => $activeTab===2,
            //'options'=>['id'=>'S3SubTab']
        ],
//         [
//            'label' => 'Статичные картинки',
//            'content' => $this->render('_statisticgraph4'),
//            'active' => $activeTab===3
//        ],
         [
            'label' => 'Траты',
            'content' => $this->render('_statisticgraph5'),
            'active' => $activeTab===3
         ],
         [
            'label' => 'Зарплата',
            'content' => app\widgets\MonthSalarySchedule\MSSchedule::widget([]),
            'active' => $activeTab===4
         ],
    ]
]);?>
</div>
<?php if (!Yii::$app->request->isPjax):?>
<?php JSRegister::begin([
    'key'      => 'select-form-script',
    'position' => \yii\web\View::POS_END
]);
?>
<script>
    let eng;

    eng=new stones.statistic.graphics({
        chartContainer:["chartContainer","chartContainer2","chartContainer3"],
        test:<?= Json::encode($test) ?>,
        managers_exists:<?= Json::encode($managers_exists) ?>,
        total:<?= Json::encode($total) ?>,
        columnType:"<?=$gModel2->columnType?>"
    });
    eng.run();

</script>
<?php JSRegister::end(); ?>
<?php endif;?>