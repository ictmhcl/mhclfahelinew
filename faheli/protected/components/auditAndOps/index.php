<?php
ini_set('display_errors', 'on');
//display_startup_errors = Off;
// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',false);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
require_once($yii);

//set default timezone
const MALDIVES_TIMEZONE = 'Asia/Karachi';
date_default_timezone_set(MALDIVES_TIMEZONE);

//Audit Components - DO NOT REMOVE - overrides Yii Core classes
Yii::$classMap= [
    'CActiveRecord' => '/protected/components/auditAndOps/CActiveRecord.php',
    'CDbCommand' => '/protected/components/auditAndOps/CDbCommand.php',
    'CDbTransaction' => '/protected/components/auditAndOps/CDbTransaction.php'
];




Yii::createWebApplication($config)->run();

