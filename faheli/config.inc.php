<?php

/**
 * THIS IS PRODUCTION SERVER SETTINGS
 */

//Under Maintenance
$cfg['maintenanceMode'] = false;


//App settings
$cfg['appName'] = 'Faheli Online Portal';
$cfg['devEmail'] = 'ict@mhcl.mv';
$cfg['adminEmail'] = 'admin@mhcl.mv';
$cfg['logoffTimeout'] = 14400; //24 mins x 60 seconds

//Development settings
$cfg['DebugMode'] = false;
$cfg['TraceLevel'] = 10;

//Application Options
$cfg['appOptions']['uploadHajjiLists'] = true;
$cfg['appOptions']['onlineSync'] = true;

//Database connections
$cfg['base_db']['host'] = 'localhost';
$cfg['base_db']['port'] = '';
$cfg['base_db']['db_name'] = 'main';
$cfg['base_db']['username'] = 'mhcl-web-user';
$cfg['base_db']['password'] = '26BB9A27956C640D5CD0C3B860BC9D0EB5EFA17D';

$cfg['errorlog_db']['host'] = 'localhost';
$cfg['errorlog_db']['port'] = '';
$cfg['errorlog_db']['db_name'] = 'main_errorlog';
$cfg['errorlog_db']['username'] = 'mhcl-web-user';
$cfg['errorlog_db']['password'] = '26BB9A27956C640D5CD0C3B860BC9D0EB5EFA17D';

$cfg['auditlog_db']['host'] = 'localhost';
$cfg['auditlog_db']['port'] = '';
$cfg['auditlog_db']['db_name'] = 'main_auditlog';
$cfg['auditlog_db']['username'] = 'mhcl-web-user';
$cfg['auditlog_db']['password'] = '26BB9A27956C640D5CD0C3B860BC9D0EB5EFA17D';

$cfg['showScriptName'] = false;
$cfg['fileUploadParentFolder'] = 'hq';
$cfg['passportService'] = '/hajjWS/web/files/';
$cfg['key'] = "Kbfd9WAYpYEITdxOeXwuR+MvFd6GAasJnmcQWKhjBSFsqHtxKUwM8eC2nXZseGeKctR7cL2V/Z4WdDQEFmIhiQ==";
$cfg['sendVerificationCode'] = true;
$cfg['processPayments'] = true;


// To goto production, remove '.uat' from the url variable below

$cfg['bml_mpg_settings'] = [
    'url' => 'https://api.merchants.bankofmaldives.com.mv/public/transactions',
    'appVersion' => '2.0',
    'apiVersion' => '2.0',
    'provider' => NULL,	
	'merchantId' => '9809692163',
	'acquirerId' => '407387',
    'responseUrl' => 'pay/process',
    'signatureMethod' => 'sha1',
    'purchaseCurrency' => 'MVR',
	'password' => 'SD1m93cM',
    'apiKey' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBJZCI6IjM1Zjg3YTIzLTM2MTEtNDUxZi1iNTBkLTExN2Y5NjZhM2RjMyIsImNvbXBhbnlJZCI6IjYwODBlZGQzZDRlNzQyMDAwOGM4YjQwNyIsImlhdCI6MTY2MjAyNDgzNSwiZXhwIjo0ODE3Njk4NDM1fQ.cs5D7q9oXgR-EiQ8XCBtYImCPW7iC5FmUj048xAyqyk'
];
