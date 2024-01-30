<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use app\controllers\ControllerMain;
use \app\controllers\ControllerTait;
use yii\filters\VerbFilter;

/**
 * Description of ZarplataController
 *
 * @author Александр
 */
class ZarplataController extends ControllerMain {

    use ControllerTait;

    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'index'       => [
                        'get'],
                    'addmonth'    => [
                        'post'],
                    'removemonth' => [
                        'post'],
                    'changevalue' => [
                        'post'],
                    'removeyear'  => ['post']
                ],
            ],
        ];
    }

    public function actions() {
        return[
            'index'       => 'app\controllers\ZarplataActions\ZarplataIndex',
            'addmonth'    => 'app\controllers\ZarplataActions\ZarplataAddMonth',
            'changevalue' => 'app\controllers\ZarplataActions\ZarplataChangeValue',
            'removemonth' => 'app\controllers\ZarplataActions\ZarplataRemoveMonth',
            'removeyear'  => 'app\controllers\ZarplataActions\ZarplataRemoveYear',
        ];
    }

}
