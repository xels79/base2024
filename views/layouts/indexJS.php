<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->registerJsFile($this->assetManager->publish('@app/web/js/URI.min.js')[1],[
        'depends' => [yii\jui\JuiAsset::className()],
        'position'=> \yii\web\View::POS_END
    ],'URI.min');
//$this->registerJsFile($this->assetManager->publish('@app/web/js/base_widget.js')[1],[
//        'depends' => [yii\jui\JuiAsset::className()],
//        'position'=> \yii\web\View::POS_END
//    ],'base_widget');
$this->registerJsFile($this->assetManager->publish('@app/web/js/banks_search.js')[1],[
        'depends' => [yii\jui\JuiAsset::className()],
        'position'=> \yii\web\View::POS_READY
    ],'banks_search');
$this->registerJsFile($this->assetManager->publish('@app/web/js/simple_form.js')[1],[
        'depends' => [\yii\web\JqueryAsset::className()],
        'position'=> \yii\web\View::POS_READY
    ],'simple_form');
//$this->registerJsFile($this->assetManager->publish('@app/web/js/parent_controller.js')[1],[
//        'depends' => [yii\jui\JuiAsset::className()],
//        'position'=> \yii\web\View::POS_END
//    ],'parent_controller');
echo $content;