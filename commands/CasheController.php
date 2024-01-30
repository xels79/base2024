<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CasheController
 *
 * @author Александр
 */
namespace app\commands;
use yii;
use yii\console\Controller;
use yii\helpers\Console;
class CasheController extends Controller{
    //put your code here
    public function actionFlush(){
        $cache=Yii::$app->cache;
        if ($cache->flush())
            echo $this->ansiFormat("Кэш очищен!\n",Console::FG_GREEN);
        else
            echo $this->ansiFormat("Ошибка!\n",Console::FG_RED);
    }
}
