<?php

define('HTTP_URL', 'http://phacil.local:131/');
define('HTTPS_URL', HTTP_URL);
define('HTTP_IMAGE', HTTP_URL);

define('USE_DB_CONFIG', false);

define('DEBUG', true);

$configs = array('PatternSiteTitle'=>' - ExacTI phacil',
				 'config_mail_protocol'=>'smtp',
				 'config_error_display' => 1,
				 'config_template' => "default",
				 'config_error_filename'=> 'error.log');

//App folders
define('DIR_APPLICATION', '/Applications/MAMP/htdocs/phacil/');
define('DIR_LOGS', DIR_APPLICATION.'logs/');
define('DIR_PUBLIC', DIR_APPLICATION.'public_html/');
define('DIR_SYSTEM', DIR_APPLICATION.'system/');
define('DIR_IMAGE', DIR_APPLICATION.'public_html/imagens/');
define('DIR_TEMPLATE', DIR_APPLICATION.'view/');
define('DIR_CACHE', DIR_APPLICATION.'cache/');

//Database Connection 
/*define('DB_DRIVER', '');
define('DB_HOSTNAME', '');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_DATABASE', '');
define('SQL_CACHE', true);*/

//Cache definitions
define('CACHE_JS_CSS', true);
//define('CACHE_use_js_plus', false);
define('CACHE_MINIFY', true);
define('CACHE_DAYS', 15);

define('CACHE_IMG', true);
define('USE_IMAGICK', true);
define('CACHE_STATIC_HTML', true);
