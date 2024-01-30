<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatisticAsset
 *
 * @author Алесандр
 */
namespace app\mainasset\navigatekey;
use yii\web\AssetBundle;

class NavigateKeyAsset extends AssetBundle{
    public $sourcePath='@app/mainasset/navigatekey';
    public $baseUrl = '@web';
    public $js = [
        'js/navigateKey.js'
    ];
    public $depends = [
        'yii\jui\JuiAsset'
    ];
}
