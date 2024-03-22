<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$cfg = $GLOBALS['cfg'];

return [
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'MHC Console App',

	// preloading 'log' component
	'preload'=> ['log'],

	'import' => [
			'application.models.*',
			'application.models.modelHelpers.*',
			'application.components.*',
		// Date behavior
			'application.behaviors.ActiveRecordDateBehavior',
	],
	// application components
	'components'=> [
			'db' => [
        'connectionString' => 'mysql:host=' . $cfg['base_db']['host'] . ($cfg['base_db']['port'] == '' ? '' : (':' . $cfg['base_db']['port'])) . ';dbname=' . $cfg['base_db']['db_name'],
        'emulatePrepare' => true,
        'username' => $cfg['base_db']['username'],
        'password' => $cfg['base_db']['password'],
        'charset' => 'utf8',
        'enableParamLogging' => true,
			],
		'log'=> [
			'class'=>'CLogRouter',
			'routes'=> [
				[
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
        ],
				[
					'class' => 'CEmailLogRoute',
					'levels' => 'error, warning, info',
					'emails' => ['ict@mhcl.mv'], // nazim@mhcl.mv,
					// mhcl mail commented because mail delivery is faster to gmail
					'subject' => 'MHCL Sync Message',
					'sentFrom' => 'admin@mhclonline.com',
					'utf8' => true,
					'except' => 'system.CModule.*'
				],
      ],
    ],
  ],
];