<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Database connection
|--------------------------------------------------------------------------
|
| These constants are used for dabase connection,
| and to use for mysqli_connect(, , , );
|
*/

//For Local
define('DB_HOST',     '127.0.0.1');
define('DB_USER',     'root');
define('DB_PASSWORD', 'root');
define('DB_NAME',     'jira_api');
define('DB_PORT',     '8889');

//For AWS
//define('DB_HOST',     'sfadcstag.ckfnyacxirbi.us-east-1.rds.amazonaws.com');
//define('DB_USER',     'dbuser');
//define('DB_PASSWORD', '3m42Giqr');
//define('DB_NAME',     'jira_api');
//define('DB_PORT',     '3306');

/*
|--------------------------------------------------------------------------
| User Defined Constants
|--------------------------------------------------------------------------
|
| URL, etc
|
*/

define('JIRA_API_BASE_URL',	'https://rakuten.atlassian.net/rest/api/2/');
define('JIRA_URL', "https://rakuten.atlassian.net");
define('USERNAME', 'data_extractor');
define('PASSWORD', 'LSLrocks123!');
define('TEMPO_API_TOKEN', '2qaw3sedrftgyhuj');
define('MAX_WORKLOG_DISPLAY', 50);
define('DEFAULT_TIME_ZONE', 'America/Los_Angeles');
define('XML_URL', 'https://rakuten.atlassian.net/sr/jira.issueviews:searchrequest-xml/temp/SearchRequest.xml');

/* End of file constants.php */
/* Location: ./application/config/constants.php */