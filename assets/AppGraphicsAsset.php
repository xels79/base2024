<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppGraphicsAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/newstyle.less',
        'css/mainbuttons.less',
        'css/calculator.less',
        'css/side2.less',
    ];
    public $js = [
        'js/baseFunction.js',
        'js/ajaxdialog2_0.js',
        'js/base_widget.js',
        'js/calculator.js',
        'js/URI.min.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\widgets\PjaxAsset',
    ];
    public $jsOptions = [
        'position' => yii\web\View::POS_END
    ];

}
