<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/dbconsole.php');

return [
    'id' => 'baseAsterionVer2',
    'name'=>'база',
    'version'=>'2.010b',
    'language'=>'ru-RU',
    'timeZone'=>'Europe/Moscow',
    'aliases'       => [
        '@vendor'=>'/var/www/u0931523/data/programs/yiicore/vendor',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@file'      => '@app/baseFiles',
        '@documents' => '@app/documents',
        '@temp'      => '@app/temp'
    ],

    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
//    'vendorPath'=>'../vendor',
    'modules' => [
        'gii' => 'yii\gii\Module',
//        'rusbankshb' => [
//            'class' => 'rusbankshb\Module',
//            'controllerNamespace' => 'rusbankshb\commands'
//        ],

    ],
//    'controllerMap'=>[
//        'rbac2' => 'app\rbac\controller\Rbac2Controller',
//    ],
    'components' => [
        'authManager' => [
           'class' => 'yii\rbac\PhpManager',
        ],

        'cache' => [
        //    'class' => 'yii\caching\FileCache',
			
            'class'        => 'yii\caching\MemCache',
            'useMemcached' => true, //$_SERVER['SERVER_SOFTWARE']!=='Apache/2.4.10 (Win64) PHP/5.6.29',
          
            'servers'      => [
                [
                    'host'   => '127.0.0.1',
                    'port'   => 11212,
                    'weight' => 64,
                ],
            ],		
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
