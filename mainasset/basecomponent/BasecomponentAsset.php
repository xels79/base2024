<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BasecomponentAsset
 *
 * @author Алесандр
 */
namespace app\mainasset\basecomponent;
use yii\web\AssetBundle;

class BasecomponentAsset extends AssetBundle{
    public $sourcePath='@app/mainasset/basecomponent';
    public $baseUrl = '@web';
    public $js = [
        'js/baseComponent.js'
    ];
}
