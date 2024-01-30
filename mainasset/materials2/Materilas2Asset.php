<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Materilas2Asset
 *
 * @author Алесандр
 */
namespace app\mainasset\materials2;
use yii\web\AssetBundle;
use yii;

class Materilas2Asset extends AssetBundle{
    public $sourcePath='@app/mainasset/materials2';
    public $baseUrl = '@web';
    public $css = [
        'less/mselect2.less'
    ];
    public $js = [
        'js/baseComponent.js',
        'js/running_line.js',
        'js/mt2_input.js',
        'js/ms2_init.js',
        'js/ms2_main.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'app\mainasset\cache\CacheAsset',
        'app\mainasset\basecomponent\BasecomponentAsset'
    ];
}
