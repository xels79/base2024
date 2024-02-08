<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id'            => 'baseAsterionVer2',
    'name'          => 'База Астерион',
    'version'       => '2.010b',
    'language'      => 'ru-RU',
    'timeZone'      => 'Europe/Moscow',
    'aliases'       => [
        //'@vendor'=>'/var/www/u0931523/data/programs/yiicore/vendor',
        '@vendor'=>'/home/xel_s/sites/tests/yiicore/vendor', 
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@file'      => '@app/baseFiles',
        '@documents' => '@app/documents',
        '@temp'      => '@app/temp'
    ],
    'basePath'      => dirname(__DIR__),
    'bootstrap'     => [
        'log'],
    'components'    => [
        'session'      => [
            'name' => 'asterionBaseVer2_01'],
        'assetManager' => [
            'linkAssets'      => true,
            'appendTimestamp' => true,
            'bundles'         => [
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => false,//'@app/web/less/',
                    'basePath'   => '@webroot',
                    'baseUrl'    => '@web',
                    'css'        => [
                        'less/bootstrap.less'],
                ],
            ],
            'converter'       => [
                'class'    => 'yii\web\AssetConverter',
                'commands' => [
                    'less' => [
                        'css',
                        //'/var/www/u0931523/data/nodejs/bin/node /var/www/u0931523/data/node_modules/less/bin/lessc {from} {to} --no-color -x ---source-map'
                        '/home/xel_s/.nvm/versions/node/v20.8.0/bin/lessc {from} {to} --no-color -x --source-map -rp="/basetest/" -ru'
                    ],
                //'ts' => ['js', 'tsc --out { to} {from}'],
                ],
            ],
        ],
        'i18n'         => [
            'translations' => [
                'app*'         => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap'        => [
                        'app'       => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'monthtoname*' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap'        => [
                        'monthtoname'       => 'monthtoname.php',
                        'monthtoname/error' => 'error.php',
                    ],
                ],
                'numbers*'     => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap'        => [
                        'numbers'       => 'numbers.php',
                        'numbers/error' => 'error.php',
                    ],
                ]
            ],
        ],
        'formatter'    => [
            'dateFormat' => 'php:d.m.Y',
            'locale'     => 'ru-Ru'
        ],
//        'authManager' => [
//            'class' => 'yii\rbac\PhpManager',
//            'defaultRoles' => ['admin','moder','desiner','proizvodstvo','logist','bugalter','guest'], // Здесь нет роли "guest", т.к. эта роль виртуальная и не присутствует в модели UserExt
//        ],
        'request'      => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '1111bf83tH9b7',
        ],
        'cache'        => [
            //'class' => 'yii\caching\FileCache',
            
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
        'user'         => [
            'identityClass'  => 'app\models\User',
            //'enableAutoLogin' => true,
            'authTimeout'    => 7200,
            'identityCookie' => [
                'name'     => '_identity',
                'httpOnly' => true,
                'domain'   => '.admin.skiy.ru',
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer'       => [
            'class'            => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
//            'transport'        => [
//                'class'      => 'Swift_SmtpTransport',
//                'host'       => 'smtp.timeweb.ru',
//                'username'   => 'base@asterionspb.ru',
//                'password'   => 'baseqwerty7000',
//                'port'       => '465', // '587',
//                'encryption' => 'SSL',
//            ],
            'transport'        => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'smtp.yandex.ru',
                'username'   => 'zakaz@asterionspb.ru',
                'password'   => 'qwerty7000',
                'port'       => '465', // '587',
                'encryption' => 'SSL',
            ],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'],
                ],
            ],
        ],
        'response'     => 'app\components\MResponse',
        'db'           => require(__DIR__ . '/db.php'),
    ],
    'controllerMap' => [
        //'viewPath'=>'app\views\admin',
        'firms' => 'app\controllers\admin\FirmsController'
    ],
    'params'        => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'      => 'yii\debug\Module', //'yiidebugModule',
        //'allowedIPs' => ['127.0.0.1', '::1','192.168.1.*','91.122.64.106'],
        'allowedIPs' => [
            '*.*.*.*'],
    ];
    //   $config['modules']['debug']='yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => [
            '127.0.0.1',
            '192.168.1.*',
            '91.122.64.106',
            '91.122.14.147'] // adjust this to your needs
    ]; //'yii\gii\Module';
}

return $config;
