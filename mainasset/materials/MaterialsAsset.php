<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\mainasset\materials;

/**
 * Description of MaterialsAsset
 *
 * @author Алесандр
 */
use yii\web\AssetBundle;
use yii;

class MaterialsAsset extends AssetBundle {
    public $sourcePath='@app/mainasset/materials';
    public $baseUrl = '@web';
    public $css = [
        'less/mselect.less'
    ];
    public $js = [
        'js/materialSelectContainer.js',
        'js/materialSelect.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'app\mainasset\cache\CacheAsset'
    ];

}
