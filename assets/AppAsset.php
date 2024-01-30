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
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/resize-table.less',
        'css/mradio-input.less',
        'css/newstyle.less',
        'css/mainbuttons.less',
        'css/calculator.less',
        'css/side2.less',
        'css/collorpicker.less',
        'css/m-radio.less',
        'css/liffCutter.less',
        'css/blockformat.less',
        'css/timePicker.less'
    ];
    public $js = [
        //'js/cache.js',
        'js/baseFunction.js',
        'js/base_widget.js',
        'js/simple_form.js',
        'js/openButton.js',
        'js/maindialog.js',
        'js/ajaxdialog.js',
        'js/ajaxdialog2_0.js',
        'js/mTitle.js',
        'js/activeDD.js',
        'js/activeDD2.js',
        'js/collorpicker.js',
        'js/tablesController.js',
        'js/filter-base.js',
        'js/filter.js',
        'js/filter-like.js',
        'js/changeStateDialogAndDoIt.js',
        //'js/colResizable-1.6.min.js',
        'js/colResizableXELS1.0.js',
        'js/jquery.maphilight.js',
//        'js/resizeble-tr.js',
        'js/myRadioButton.js',
        'js/timePicker.js',
        'js/zakaz.js',
        'js/resizeble-table.js',
        'js/material-to-order.js',
        'js/dirt-zakaz.js',
        'js/pechatnics.js',
        'js/disainer-zakaz.js',
        'js/bugalter-zakaz.js',
        'js/bugalter-post-zakaz.js',
        'js/zakaz-list.js',
        'js/firm-addchange.js',
        'js/firm-list.js',
        'js/material-table.js',
        'js/onlyNumInput.js',
        'js/banks_search.js',
        'js/parent_controller.js',
        'js/calculator.js',
        'js/URI.min.js',
        'js/view_dialog.js',
        'js/tecnicalsOptions.js',
        'js/setupUser.js',
        'js/blockformat.js',
        'js/rekvisitController.js',
        'js/zametkitController.js',
        
//        'js/materials/materialSelectContainer.js',
//        'js/materials/materialSelect.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\widgets\PjaxAsset',
        'app\mainasset\cache\CacheAsset',
    ];
    public $jsOptions = [
        'position' => yii\web\View::POS_END
    ];

}
