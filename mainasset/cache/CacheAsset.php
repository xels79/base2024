<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\mainasset\cache;

/**
 * Description of MaterialsAsset
 *
 * @author Алесандр
 */
use yii\web\AssetBundle;
use yii;

class CacheAsset extends AssetBundle {
    public $sourcePath='@app/mainasset/cache';
    public $baseUrl = '@web';
    public $js = [
        'js/cache.js',
    ];
}
