<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\MonthSalarySchedule\assets;

/**
 * Description of MonthlSsalaryScheduleAssets
 *
 * @author Алесандр
 */
use yii\web\AssetBundle;
use yii;

class MSScheduleAssets  extends AssetBundle{
    public $sourcePath='@app/widgets/MonthSalarySchedule/assets';
    public $baseUrl = '@web';
    public $css = [
        'less/MSSStyle.less'
    ];
    public $js = [
        'js/mss.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'app\mainasset\basecomponent\BasecomponentAsset'
    ];

}
