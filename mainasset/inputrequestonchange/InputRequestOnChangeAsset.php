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
namespace app\mainasset\inputrequestonchange;
use yii\web\AssetBundle;
use Yii;

class InputRequestOnChangeAsset extends AssetBundle{
    public $sourcePath='@app/mainasset/inputrequestonchange';
    public $baseUrl = '@web';
    public $js = [
        'js/inputrequestonbuner.js',
        'js/inputrequestonchange.js',
        'js/inputrequestonadd.js',
        'js/inputrequestonremove.js'
    ];
    public $depends = [
        'yii\jui\JuiAsset'
    ];
    public function init(){
        parent::init();
        $red=Yii::$app->assetManager->publish($this->sourcePath.'/pic/red_arr.png');
        $yellow=Yii::$app->assetManager->publish($this->sourcePath.'/pic/yellow_arr.png');
        $green=Yii::$app->assetManager->publish($this->sourcePath.'/pic/green_arr.png');
        Yii::$app->view->registerJs('$.custom.inputROCOptions.setOptions('
                . '"'.$red[1].'",'
                . '"'.$yellow[1].'",'
                . '"'.$green[1].'",'
                . ');',yii\web\View::POS_READY);
    }
}
