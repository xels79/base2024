<?php
$pth=realpath(__DIR__ .'/../../').'/yiicore';
// comment out the following two lines when deployed to production
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'dev');

require "$pth/vendor/autoload.php";
require "$pth/vendor/yiisoft/yii2/Yii.php";
 
$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
