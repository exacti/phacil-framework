<?php
/**
 * Copyright (c) 2019. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

class startEngineExacTI {
	public $constants;
	public $userConstants;
	public $phpversion;
	protected $includes;
	protected $dirs;
	
	public function __construct () {
		$this->constants = get_defined_constants(true);
		$this->userConstants = $this->constants['user'];
		$this->includes = get_included_files();
		
		// Check Version
		$this->phpversion = $this->checkPHPversion();
		
		//Check Config Load
		$loadConfig = $this->checkConfigFile();
		
		if($loadConfig) {
			$this->defineAuxConstants();
		}
		
		if(defined('DEBUG') && DEBUG == true) {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}
		
		$this->loadengine();
	}
	
	private function checkPHPversion() {
		if (version_compare(phpversion(), '5.3.0', '>') == false) {
			exit('PHP 5.3+ Required');
		} else {
			return phpversion();
		}
	}
	
	private function checkConfigFile() {
		
		if (!$this->checkConstantsRequired()) {
			try {
				$baseDir = str_replace('system', '', __DIR__);
				include_once($baseDir."config.php");
				
				if (!$this->checkConstantsRequired()) {
					throw new \Exception("Can't load minimun config constants, please check your config file!");
				}
				
			} catch (Exception $e) {
				exit($e->getMessage());
			}
			
		} else {
			return true;
		}
		
	}
	
	private function checkConstantsRequired () {
		if (!defined('DIR_APPLICATION') || !defined('DIR_SYSTEM') || !defined('DIR_PUBLIC') || !defined('DIR_TEMPLATE') || !defined('USE_DB_CONFIG')) {
			return(false);
		} else {
			return(true);
		}
	}
	
	private function defineAuxConstants () {
		(defined('HTTP_URL')) ? define('HTTP_SERVER', HTTP_URL) : '';
		(defined('HTTPS_URL')) ? define('HTTPS_SERVER', HTTPS_URL) : '';
	}
	
	private function loadengine () {
		$this->dirs = glob(DIR_SYSTEM.'*/autoload.php', GLOB_BRACE);
		foreach($this->dirs as $key => $value) {
			if($value != ""){
				try {
					if(is_readable($value)) {
						require_once $value;
					} else {
						throw new \Exception("I can't load '$value' file! Please check system permissions.");
					}
				} catch (Exception $e) {
					exit($e->getMessage());
				}
				
			}
		}
	}

	public function setTimezone($utc) {

	    try {
            $tzc = @date_default_timezone_set($utc);
            if (!$tzc){
                throw new \ErrorException($utc. " not found in PHP Compiler.");
            }
        } catch (\ErrorException $e) {
	        $trace = ($e->getTrace());

            echo PHP_EOL.'Timezone Error: ',  $e->getMessage() ." on ". $trace[0]['file'] ." in line ". $trace[0]['line'].".",  PHP_EOL;
        }

    }

    public function getTimezone(){
        return date_default_timezone_get();
    }

    public function listTimezones() {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

    public function version() {
	    return file_get_contents(DIR_SYSTEM."engine/VERSION");
    }

    public function extraRegistrations() {
        if(file_exists(DIR_SYSTEM."registrations.php"))
            include(DIR_SYSTEM."registrations.php");
    }
	
}

$engine = new startEngineExacTI();

// Registry
$registry = new Registry();
$registry->set('engine', $engine);

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

$registry->set('db', $db);

// Settings
if(!empty($configs)){
	foreach ($configs as $key => $confValue) {
		$config->set($key, $confValue);
	}
}

if(USE_DB_CONFIG === true) {

    $query = (defined('CUSTOM_DB_CONFIG')) ? $db->query(CUSTOM_DB_CONFIG) : $db->query("SELECT * FROM settings ORDER BY setting_id ASC");

	foreach ($query->rows as $setting) {
		if (!$setting['serialized']) {
			$config->set($setting['key'], $setting['value']);
		} else {
			$config->set($setting['key'], unserialize($setting['value']));
		}
	}
}


$config->set('config_url', HTTP_URL);
$config->set('config_ssl', HTTPS_URL);

//timezone
if($config->get('date_timezone')){
    $engine->setTimezone($config->get('date_timezone'));
}

// Site Title
if($config->get('PatternSiteTitle') == true) {
	define('PATTERSITETITLE', $config->get('PatternSiteTitle'));
} else {
	define('PATTERSITETITLE', false);
}

// Url
$url = new Url($config->get('config_url'), $config->get('config_use_ssl') ? $config->get('config_ssl') : $config->get('config_url'));	
$registry->set('url', $url);

// Log 
if(!$config->get('config_error_filename')){
	$config->set('config_error_filename', 'error.log');
}

$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

function error_handler($errno, $errstr, $errfile, $errline) {
	global $log, $config;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}
		
	if ($config->get('config_error_display')) {
		echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}
	
// Error Handler
set_error_handler('error_handler');

//Caches
$caches = new Caches();
$registry->set('cache', $caches);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response); 

// Session
$session = new Session();
$registry->set('session', $session);

// Translate
$translate = new Translate();
$registry->set('translate', $translate);

// E-Mail Config
$mail = new Mail();
$mail->protocol = $config->get('config_mail_protocol');
if($config->get('config_mail_protocol') == 'smtp'){
	$mail->parameter = $config->get('config_mail_parameter');
	$mail->hostname = $config->get('config_smtp_host');
	$mail->username = $config->get('config_smtp_username');
	$mail->password = $config->get('config_smtp_password');
	$mail->port = $config->get('config_smtp_port');
	$mail->timeout = $config->get('config_smtp_timeout');	
}
$registry->set('mail', $mail); 
			
// Document
$document = new Document();
$registry->set('document', $document); 	

// Custom registrations
$engine->extraRegistrations();

// Front Controller 
$controller = new Front($registry);

// SEO URL's
$controller->addPreAction(new ActionSystem('url/seo_url'));	
	
// Router
if (isset($request->get['route'])) {
	$action = new Action($request->get['route']);
} else {
    $default = (defined('DEFAULT_ROUTE')) ? DEFAULT_ROUTE : 'common/home';
    $request->get['route'] = $default;
	$action = new Action($default);
}

// Dispatch
$not_found = (defined('NOT_FOUND')) ? NOT_FOUND : 'error/not_found';
$controller->dispatch($action, new Action($not_found));

// Output
$response->output();

