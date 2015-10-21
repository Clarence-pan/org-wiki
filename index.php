<?php

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', $_GET['debug'] === 'enable' or $_COOKIE['debug'] === 'enable');

if (YII_DEBUG){
    ini_set('display_errors', 'on');
}

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

// change the following paths if necessary
$yii = dirname(__FILE__) . '/../framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();

