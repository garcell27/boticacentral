<?php
date_default_timezone_set('America/Lima');
ini_set('display_errors', '1');
ini_set('memory_limit', '256M');
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require (__DIR__ .'/../sistemabt/vendor/autoload.php');
require (__DIR__ .'/../sistemabt/vendor/yiisoft/yii2/Yii.php');

$config = require (__DIR__ .'/../sistemabt/config/web.php');

(new yii\web\Application($config))->run();
