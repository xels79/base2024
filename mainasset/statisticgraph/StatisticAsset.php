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
namespace app\mainasset\statisticgraph;
use yii\web\AssetBundle;

class StatisticAsset extends AssetBundle{
    public $sourcePath='@app/mainasset/statisticgraph';
    public $baseUrl = '@web';
    public $css = [
        'less/graphic.less'
    ];
    public $js = [
        'js/canvasjs-3.2.9/canvasjs.min.js',
        'js/statisticgraphCanvas.js',
        'js/statisticgraph.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'app\mainasset\basecomponent\BasecomponentAsset'
    ];
}
