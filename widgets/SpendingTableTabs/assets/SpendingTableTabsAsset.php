<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\widgets\SpendingTableTabs\assets;
/**
 * Description of SpendingTableAsset
 *
 * @author Алесандр
 */
use yii\web\AssetBundle;
use yii;

class SpendingTableTabsAsset extends AssetBundle{
    public $sourcePath='@app/widgets/SpendingTableTabs/assets';
    public $baseUrl = '@web';
    public $css = [
        'less/stTab.less'
    ];
    public $js = [
        'js/stTabs.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];
}
