<?php
/**
 * Copyright (c) 2019. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/**
 * 
 * @package Phacil\Framework
 */
final class startEngineExacTI {

    /**
     * 
     * @var string|false
     */
    public $phpversion;
    //protected $includes;

    /**
     * 
     * @var array
     */
    protected $dirs;
    
    /**
     * 
     * @var Registry
     */
    public $registry;

    /**
     * 
     * @var false|ActionSystem
     */
    private $preActions = false;

    /**
     * @return void 
     * @throws Exception 
     * @throws TypeError 
     */
    public function __construct () {
        //$this->constants = get_defined_constants(true);
        //$this->userConstants = $this->constants['user'];
        //$this->includes = get_included_files();

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

        // Registry
        $this->registry = new Registry();
    }

    /**
     * @param string $key 
     * @return object 
     */
    public function __get($key) {
        return $this->registry->get($key);
    }

    /**
     * @param string $key 
     * @param object $value 
     * @return void 
     */
    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    /** @return string|false  */
    private function checkPHPversion() {
        if (version_compare(phpversion(), '5.4.40', '>') == false) {
            exit('PHP 5.4+ Required');
        } else {
            return phpversion();
        }
    }

    /** @return true|void  */
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

    /** @return bool  */
    private function checkConstantsRequired () {
        /* $dbConsts = ['DB_DRIVER' => 'nullStatement', 'DB_HOSTNAME' => NULL, 'DB_USERNAME' => NULL, 'DB_PASSWORD' => NULL, 'DB_DATABASE' => NULL];

         foreach ($dbConsts as $constDB => $value) {
            if (!defined($constDB)) {
                define($constDB, $value);
            }
        } */

        if (!defined('DIR_APPLICATION') || !defined('DIR_SYSTEM') || !defined('DIR_PUBLIC') || !defined('DIR_TEMPLATE') || !defined('USE_DB_CONFIG')) {
            return(false);
        } else {
            return(true);
        }
    }

    /** @return void  */
    private function defineAuxConstants () {
        (defined('HTTP_URL')) ? define('HTTP_SERVER', HTTP_URL) : '';
        (defined('HTTPS_URL')) ? define('HTTPS_SERVER', HTTPS_URL) : '';
    }

    /**
     * @return void 
     * @throws TypeError 
     */
    private function loadengine () {
        //$this->dirs = glob(DIR_SYSTEM.'*/autoload.php', GLOB_BRACE);
        
        //require_once (DIR_SYSTEM.'database/autoload.php');

        require_once (DIR_SYSTEM.'engine/autoload.php');
    }

    /**
     * @param string $utc 
     * @return void 
     */
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

    /** @return string  */
    public function getTimezone(){
        return date_default_timezone_get();
    }

    /** @return array|false  */
    public function listTimezones() {
        return \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
    }

    /** @return string|false  */
    public function version() {
        return file_get_contents(DIR_SYSTEM."engine/VERSION");
    }

    /** @return void  */
    public function extraRegistrations() {

        if(file_exists(DIR_SYSTEM."registrations.php"))
            include(DIR_SYSTEM."registrations.php");
    }

    /** @return array  */
    public function constants(){
        return get_defined_constants(true);
    }

    /** @return array  */
    public function userConstants() {
        return $this->constants()['user'];
    }

    /**
     * @param string $constant 
     * @param string $group 
     * @return mixed 
     */
    public function constantName($constant, $group = 'user') {

        foreach ($this->constants()[$group] as $name => $value){
            if($constant === $value)
                return $name;
        }

        return $constant;
    }

    /** @return array  */
    public function controllerPreActions() {
        return (isset($this->preActions) && is_array($this->preActions)) ? $this->preActions : [];
    }

    public function checkRegistry($key){
        //mail
		if(!isset($this->registry->$key) && $key == 'mail'){
			$this->mail = new Mail();
			$this->mail->protocol = $this->config->get('config_mail_protocol');
			if($this->config->get('config_mail_protocol') == 'smtp'){
				$this->mail->parameter = $this->config->get('config_mail_parameter');
				$this->mail->hostname = $this->config->get('config_smtp_host');
				$this->mail->username = $this->config->get('config_smtp_username');
				$this->mail->password = $this->config->get('config_smtp_password');
				$this->mail->port = $this->config->get('config_smtp_port');
				$this->mail->timeout = $this->config->get('config_smtp_timeout');
			}
		}

		// Translate
		if(!isset($this->registry->$key) && $key == 'translate'){
			$this->translate = new Translate();
		}

		// Session
		if(!isset($this->registry->$key) && $key == 'session'){
			$this->session = new Session();
		}

        return (isset($this->registry->$key)) ? $this->registry->$key : NULL;
    }

}

/**
 * @global startEngineExacTI $engine
 */
global $engine;

/** 
 * @global \Phacil\Framework\startEngineExacTI $engine 
 * */
$engine = new startEngineExacTI();

// Registry
/** @var \Phacil\Framework\startEngineExacTI $engine */
$engine->engine = $engine;

// Loader
/**
 * @var Loader
 */
$engine->load = new Loader($engine->registry);

// Config
/** @var Config */
$engine->config = new Config();

if(defined('DB_DRIVER'))
    $engine->db = new Database(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Settings
if(!empty($configs)){
    foreach ($configs as $key => $confValue) {
        $engine->config->set($key, $confValue);
    }
}

if(USE_DB_CONFIG === true) {

    $query = (defined('CUSTOM_DB_CONFIG')) ? $engine->db->query(CUSTOM_DB_CONFIG) : $engine->db->query("SELECT * FROM settings ORDER BY setting_id ASC");

    foreach ($query->rows as $setting) {
        if (!$setting['serialized']) {
            $engine->config->set($setting['key'], $setting['value']);
        } else {
            $engine->config->set($setting['key'], unserialize($setting['value']));
        }
    }
}


$engine->config->set('config_url', HTTP_URL);
$engine->config->set('config_ssl', HTTPS_URL);

//timezone
if($engine->config->get('date_timezone')){
    $engine->setTimezone($engine->config->get('date_timezone'));
}

// Site Title
if($engine->config->get('PatternSiteTitle') == true) {
    define('PATTERSITETITLE', $engine->config->get('PatternSiteTitle'));
} else {
    define('PATTERSITETITLE', false);
}

// Url
$engine->url =  new Url($engine->config->get('config_url'), $engine->config->get('config_use_ssl') ? $engine->config->get('config_ssl') : $engine->config->get('config_url'));

// Log
if(!$engine->config->get('config_error_filename')){
    $engine->config->set('config_error_filename', 'error.log');
}

/**
 * @var Log
 */
$engine->log = new Log($engine->config->get('config_error_filename'));

// Error Handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($engine){

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
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $error = 'Deprecated';
            break;
        default:
            $error = $engine->constantName($errno, 'Core');
            break;
    }

    if ($engine->config->get('config_error_display')) {
        echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
    }

    if ($engine->config->get('config_error_log')) {
        $engine->log->write( $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline.' | Phacil '.$engine->version(). ' on PHP '.$engine->phpversion);
    }

    return true;
});

set_exception_handler(function($e) use ($engine) {
    if ($engine->config->get('config_error_display')) {
        echo '<b>' . get_class($e) . '</b>: ' . $e->getMessage() . ' in <b>' . $e->getFile() . '</b> on line <b>' . $e->getLine() . '</b>';
    }

    if ($engine->config->get('config_error_log')) {
        $engine->log->write(get_class($e) . ':  ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    }
});

/**
 * Caches
 * @var Caches
 */
$engine->cache = new Caches();

/**
 * Request
 * @var Request
 */
$engine->request = new Request();

// Response
/* $response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($engine->config->get('config_compression')); */
$engine->response = new Response();
$engine->response->addHeader('Content-Type: text/html; charset=utf-8');

if($engine->config->get('config_compression'))
    $engine->response->setCompression($engine->config->get('config_compression'));

// Session
$engine->session = new Session(
    $engine->config->get('session_redis'), 
    $engine->config->get('session_redis_dsn'), 
    $engine->config->get('session_redis_port'), 
    $engine->config->get('session_redis_password'), 
    $engine->config->get('session_redis_expire'), 
    $engine->config->get('session_redis_prefix')
);

// Document
$engine->document = new Document();

// Custom registrations
$engine->extraRegistrations();

// Front Controller
$frontController = new Front($engine->registry);

// SEO URL's
$frontController->addPreAction(new ActionSystem((string) 'url/seo_url'));

//extraPreactions
if($engine->controllerPreActions()){
    foreach ($engine->controllerPreActions() as $action){
        $frontController->addPreAction(new Action($action));
    }
}

// Router
if (isset($engine->request->get['route'])) {
    $action = new Action($engine->request->get['route']);
} else {
    $default = (defined('DEFAULT_ROUTE')) ? DEFAULT_ROUTE : 'common/home';
    $engine->request->get['route'] = $default;
    $action = new Action($default);
}

// Dispatch
$not_found = (defined('NOT_FOUND')) ? NOT_FOUND : 'error/not_found';
$frontController->dispatch($action, ($not_found));

// Output
$engine->response->output();
