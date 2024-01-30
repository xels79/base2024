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

class AppAssetUsers extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/newstyle.less',
        'css/calculator.less',
        'css/side2.less',
        'css/collorpicker.less',
        'css/userslist.less'
    ];
    public $js = [
        'js/baseFunction.js',
        'js/openButton.js',
        'js/maindialog.js',
        'js/ajaxdialog.js',
        'js/activeDD.js',
        'js/onlyNumInput.js',
        'js/URI.min.js',
        'js/view_dialog.js',
        'js/base_widget.js',
        'js/setupUser.js',
        'js/calculator.js',
        'js/tablesController.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'app\mainasset\cache\CacheAsset',
    ];
    public $jsOptions = [
        'position' => yii\web\View::POS_END
    ];

}
