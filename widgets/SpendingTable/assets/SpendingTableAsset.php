<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\widgets\SpendingTable\assets;
/**
 * Description of SpendingTableAsset
 *
 * @author Алесандр
 */
use yii\web\AssetBundle;
use yii;

class SpendingTableAsset extends AssetBundle{
    public $sourcePath='@app/widgets/SpendingTable/assets';
    public $baseUrl = '@web';
    public $css = [
        'less/st.less'
    ];
    public $js = [
        'js/st.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'app\mainasset\navigatekey\NavigateKeyAsset',
        'app\mainasset\inputrequestonchange\InputRequestOnChangeAsset',
        'app\mainasset\basecomponent\BasecomponentAsset'
    ];
}
