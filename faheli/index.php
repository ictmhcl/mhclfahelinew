<?php
//echo "Under MAINTENANCE! Please try tomorrow!"; exit();
// import configuration file
require_once(dirname(__FILE__).'/config.inc.php');
// if ($GLOBALS['cfg']['maintenanceMode']) {

    //TODO: Show a view instead
    // echo "UNDER MAINTENANCE - Sorry about the inconvenience. Please check back later in 20 minutes. Thank you!";
    // exit;
// }

require_once(dirname(__FILE__).'/root_functions.php');

// display errors if in Debug mode
ini_set('display_errors', ($GLOBALS['cfg']['DebugMode']?'on':'off'));

// determine yii config file
$isApiRequest = !empty($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !=
 "/" && explode('/',$_SERVER['PATH_INFO'])[1] == "api";
$configFile = $isApiRequest?"api.php":"main.php";
//$configFile = "main.php";


$yii=dirname(__FILE__).'/../../Yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/' . $configFile;

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',$GLOBALS['cfg']['DebugMode']);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',$GLOBALS['cfg']['TraceLevel']);

require_once($yii);

//Audit Components - DO NOT REMOVE - overrides Yii Core classes
Yii::$classMap= [
    'CActiveRecord' => dirname(__FILE__) . '/protected/components/auditAndOps/CActiveRecord.php',
    'CDbCommand' => dirname(__FILE__) . '/protected/components/auditAndOps/CDbCommand.php',
    'CDbTransaction' => dirname(__FILE__) . '/protected/components/auditAndOps/CDbTransaction.php',
];

//Extended Grid View - DO NOT REMOVE - overrides Yii Core Class
//Yii::$classMap['CBaseListView'] = dirname(__FILE__)
//  .'/protected/components/EBaseListView/EBaseListView.php';
//Yii::$classMap['CGridView'] = dirname(__FILE__)
//  .'/protected/components/EBaseListView/CGridView.php';

Yii::createWebApplication($config)->run();
