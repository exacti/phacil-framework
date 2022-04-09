<?php
/**
 * Copyright (c) 2019. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/**
 * Start engine class
 * 
 * @since 1.0.0
 * @package Phacil\Framework
 */
final class startEngineExacTI {

    /**
     * Storage the PHP version
     * 
     * @var string|false
     */
    public $phpversion;
    //protected $includes;

    /**
     * Loaded paths to autoload. 
     * 
     * @see engine/autoload.php
     * @since 1.3.0
     * @deprecated 2.0.0
     * @var array
     */
    protected $dirs = [];
    
    /**
     * Instance of all engine elements
     * 
     * @var Registry
     * 
     * @since 1.0.0
     */
    public $registry;

    /**
     * System pre actions loader
     * 
     * @var false|ActionSystem|\Phacil\Framework\Interfaces\Action
     * @since 1.5.1
     */
    private $preActions = false;

    /**
     * @return void 
     * @throws Exception 
     * @throws TypeError 
     */
    public function __construct () {

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

    /** 
     * Check if system have a minimum PHP requirement
     * @since 1.0.0
     * @return string|false 
     */
    private function checkPHPversion() {
        if (version_compare(phpversion(), '5.4.40', '>') == false) {
            trigger_error('PHP 5.4.40+ Required', E_ERROR);
        } else {
            return phpversion();
        }
    }

    /**
     * Check if config file is present loadable
     * @return true|void  
     * @since 1.0.0
     */
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

    /** 
     * Check if have the required minimum configurations
     * @return bool  
     * @since 1.0.0
     */
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

    /** 
     * Define compatibility constants
     * @return void  
     * @since 1.0.0
     */
    private function defineAuxConstants () {
        (defined('HTTP_URL')) ? define('HTTP_SERVER', HTTP_URL) : '';
        (defined('HTTPS_URL')) ? define('HTTPS_SERVER', HTTPS_URL) : '';
    }

    /**
     * Load the autoload SPL engine
     * @return void 
     * @throws Exception 
     * @since 1.0.0
     */
    private function loadengine () {
        //$this->dirs = glob(DIR_SYSTEM.'*/autoload.php', GLOB_BRACE);
        
        //require_once (DIR_SYSTEM.'database/autoload.php');

        require_once (DIR_SYSTEM.'engine/autoload.php');
    }

    /**
     * Define the timezone
     * @param string $utc 
     * @return void 
     * @since 1.2.1
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

    /** 
     * Get the timezone
     * @since 1.2.1
     * @return string  
     */
    public function getTimezone(){
        return date_default_timezone_get();
    }

    /** 
     * List system available timezones
     * @since 1.2.1
     * @return array|false  
     */
    public function listTimezones() {
        return \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
    }

    /** 
     * Return Phacil Framework version
     * @since 1.3.1
     * @return string|false  
     */
    public function version() {
        return file_get_contents(DIR_SYSTEM."engine/VERSION");
    }

    /** 
     * Include extra registration file
     * @since 1.3.2
     * @return void  
     */
    public function extraRegistrations() {

        if(file_exists(\Phacil\Framework\Config::DIR_SYSTEM()."registrations.php"))
            include(DIR_SYSTEM."registrations.php");
    }

    /** 
     * Return the defined constants
     * @return array 
     * @since 1.5.0
     */
    public function constants(){
        return get_defined_constants(true);
    }

    /** 
     * Return only user defined constants
     * @return array  
     * @since 1.5.0
     */
    public function userConstants() {
        return $this->constants()['user'];
    }

    /**
     * Return defined constant based as group
     * @param string $constant 
     * @param string $group 
     * @return mixed 
     * @since 1.5.0
     */
    public function constantName($constant, $group = 'user') {

        foreach ($this->constants()[$group] as $name => $value){
            if($constant === $value)
                return $name;
        }

        return $constant;
    }

    /** 
     * Add controller system pre-actions
     * @return array 
     * @since 1.5.1
     */
    public function controllerPreActions() {
        return (isset($this->preActions) && is_array($this->preActions)) ? $this->preActions : [];
    }

    /**
     * Check the registry element
     * 
     * @since 2.0.0
     * @param string $key 
     * @return true|null 
     */
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

        return (isset($this->registry->$key)) ?: NULL;
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
    $engine->db = new Database(\Phacil\Framework\Config::DB_DRIVER(), \Phacil\Framework\Config::DB_HOSTNAME(), \Phacil\Framework\Config::DB_USERNAME(), \Phacil\Framework\Config::DB_PASSWORD(), \Phacil\Framework\Config::DB_DATABASE());

// Settings
if(!empty($configs)){
    foreach ($configs as $key => $confValue) {
        $engine->config->set($key, $confValue);
    }
}

if(\Phacil\Framework\Config::USE_DB_CONFIG() === true) {

    $query = (\Phacil\Framework\Config::CUSTOM_DB_CONFIG()) ? $engine->db->query(\Phacil\Framework\Config::CUSTOM_DB_CONFIG()) : $engine->db->query("SELECT * FROM settings ORDER BY setting_id ASC");

    foreach ($query->rows as $setting) {
        if (!$setting['serialized']) {
            $engine->config->set($setting['key'], $setting['value']);
        } else {
            $engine->config->set($setting['key'], unserialize($setting['value']));
        }
    }
}


$engine->config->set('config_url', \Phacil\Framework\Config::HTTP_URL());
$engine->config->set('config_ssl', \Phacil\Framework\Config::HTTPS_URL());

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

//extraPreActions
if($engine->controllerPreActions()){
    foreach ($engine->controllerPreActions() as $action){
        $frontController->addPreAction(new Action($action));
    }
}

// Router
if (isset($engine->request->get['route'])) {
    $action = new Action($engine->request->get['route']);
} else {
    $default = (\Phacil\Framework\Config::DEFAULT_ROUTE()) ? \Phacil\Framework\Config::DEFAULT_ROUTE() : \Phacil\Framework\Config::DEFAULT_ROUTE('common/home');
    $engine->request->get['route'] = $default;
    $action = new Action($default);
}

// Dispatch
$not_found = (\Phacil\Framework\Config::NOT_FOUND()) ? \Phacil\Framework\Config::NOT_FOUND() : \Phacil\Framework\Config::NOT_FOUND('error/not_found');
$frontController->dispatch($action, ($not_found));

// Output
$engine->response->output();
