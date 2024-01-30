<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\SpendingToYourself\assets;

/**
 * Description of SpendingToYourselfAsset
 *
 * @author Professional
 */
use yii\web\AssetBundle;

class SpendingToYourselfAsset extends AssetBundle{
    public $sourcePath='@app/widgets/SpendingToYourself/assets';
    public $baseUrl = '@web';
    public $css = [
        //'less/st.less'
    ];
    public $js = [
        'js/STY.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'app\mainasset\inputrequestonchange\InputRequestOnChangeAsset',
        'app\mainasset\basecomponent\BasecomponentAsset',
        'app\assets\AppAsset'
    ];
}
