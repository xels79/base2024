<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\assets;

/**
 * Description of AppAssetUsers
 *
 * @author alex
 */
use yii\web\AssetBundle;
use yii;

class AppAssetZarplata extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/zarplata.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
    ];
    public $jsOptions = [
        'position' => yii\web\View::POS_END
    ];

}
