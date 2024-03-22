<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$cfg = $GLOBALS['cfg'];

return [
  'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
  'name' => $cfg['appName'],
  'timezone' => 'Indian/Maldives',
  //source language
  'sourceLanguage' => 'en',
  'defaultController' => 'attendance',
  // preloading 'log' component
  'preload' => ['log', 'audit'],
  // autoloading model and component classes
  'import' => [
    'application.models.*',
    'application.models.modelHelpers.*',
    'application.components.*',
    // Date behavior
    'application.behaviors.ActiveRecordDateBehavior',
    //Audit & Op logs
    'application.components.auditAndOps.AuditLog',
    'application.components.auditAndOps.AuditLogActionTypes',
    'application.components.auditAndOps.AuditLogDataTypes',
    'application.components.auditAndOps.AuditOpLog',
    'application.components.auditAndOps.OperationLogs',
    'application.components.auditAndOps.OperationTypes',
    'application.components.auditAndOps.Audits',
    'application.components.auditAndOps.ClientAudit',
    'application.components.auditAndOps.TransactionLogs',
  ],
  'modules' => array_merge(
    (isset($cfg['modules']) ? $cfg['modules'] : []), []
  ),
  // application components
  'components' => [
    // session handling
    'session' => [
      'autoStart' => false,
      'cookieMode' => "none",
    ],
    // request defaults
    'request' => [
      'enableCookieValidation' => false,
    ],
    // cleanse input
    'input' => [
      'class' => 'CmsInput',
      'cleanPost' => true,
      'cleanGet' => true,
    ],
    // audit
    'audit' => [
      'class' => 'AuditOpLog',
      'rawLog' => false,
      'collectChildren' => true,
      'opLog' => true,
    ],
    // authentication
    'user' => [
      'class' => 'WebUser',
      // enable cookie-based authentication
      'allowAutoLogin' => false,
    ],
    // uncomment the following to enable URLs in path-format
    'urlManager' => [
      'urlFormat' => 'path',
      'showScriptName' => $cfg['showScriptName'],
      'rules' => [
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
      ],
    ],
    // db connection
    'db' => [
      'connectionString' => 'mysql:host=' . $cfg['base_db']['host'] . ($cfg['base_db']['port'] == '' ? '' : (':' . $cfg['base_db']['port'])) . ';dbname=' . $cfg['base_db']['db_name'],
      'emulatePrepare' => true,
      'username' => $cfg['base_db']['username'],
      'password' => $cfg['base_db']['password'],
      'charset' => 'utf8',
      'enableParamLogging' => true,
    ],
    'db_attendance' => [
      'connectionString' => 'mysql:host=' . $cfg['attendance_db']['host'] . ($cfg['attendance_db']['port'] == '' ? '' : (':' . $cfg['attendance_db']['port'])) . ';dbname=' . $cfg['attendance_db']['db_name'],
      'emulatePrepare' => true,
      'username' => $cfg['attendance_db']['username'],
      'password' => $cfg['attendance_db']['password'],
      'charset' => 'utf8',
      'class' => 'CDbConnection'
    ],
    'db_errorlog' => [
      'connectionString' => 'mysql:host=' . $cfg['errorlog_db']['host'] . ($cfg['errorlog_db']['port'] == '' ? '' : (':' . $cfg['errorlog_db']['port'])) . ';dbname=' . $cfg['errorlog_db']['db_name'],
      'emulatePrepare' => true,
      'username' => $cfg['errorlog_db']['username'],
      'password' => $cfg['errorlog_db']['password'],
      'charset' => 'utf8',
      'class' => 'CDbConnection'
    ],
    'db_audit' => [
      'connectionString' => 'mysql:host=' . $cfg['auditlog_db']['host'] . ($cfg['auditlog_db']['port'] == '' ? '' : (':' . $cfg['auditlog_db']['port'])) . ';dbname=' . $cfg['auditlog_db']['db_name'],
      'emulatePrepare' => true,
      'username' => $cfg['auditlog_db']['username'],
      'password' => $cfg['auditlog_db']['password'],
      'charset' => 'utf8',
      'class' => 'CDbConnection'
    ],
    'errorHandler' => (//YII_DEBUG?null:
      ['errorAction' => 'site/error']
    ),
    'log' => [
      'class' => 'CLogRouter',
      'routes' => [
        [
          'class' => 'CFileLogRoute',
          'levels' => 'error, warning',
        ],
        // uncomment the following to show log messages on web pages
//        [
//            'class' => 'CWebLogRoute',
//            'categories' => 'system.db.CDbCommand'
//        ],
      ],
    ],
  ],
  // application-level parameters that can be accessed
  // using Yii::app()->params['paramName']
  'params' => [
    'dateTime' => (new DateTime('now'))->format('Y-m-d H:i:s'),
    'passportService' => $cfg['passportService'],
    'devEmail' => $cfg['devEmail'],
    'adminEmail' => $cfg['adminEmail'],
    'appOptions' => $cfg['appOptions'],
    'jwtKey' => $cfg['key'],
  ],
];
