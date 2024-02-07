<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework;

 /**
  * Autoload class for Phacil Framework
  *
  * @package Phacil\Framework
  * @since 2.0.0
  */
 class AutoLoad {

	/**
	 * 
	 * @var array|null
	 */
	private static $namespace = null;

	/**
	 * 
	 * @var array|null
	 */
	private static $namespaceWithoutPrefix;

	/**
	 * 
	 * @var string
	 */
	private static $class = null;

	private static $subCallClass = [];

	const SEPARATOR = "\\";

	/**
	 * 
	 * @var string
	 */
	private static $classNative;

	/** 
	 * Config class
	 */
	const CONFIG_CLASS = "Phacil\Framework\Config";

	/**
	 * 
	 * @var \Phacil\Framework\AutoLoad
	 */
	private static $instance = null;

	/**
	 * 
	 * @var array
	 */
	protected $loadedClasses = array();

	/**
	 * 
	 * @var bool
	 */
	private static $composerLoaded = false;

	/**
	 * 
	 * @var array
	 */
	private static $allowed = [
		'Log',
		'Front',
		'Controller',
		'Loader',
		'Model',
		'Registry',
		'Document',
		'Response',
		'Classes',
		'AbstractHelper',
		'ActionSystem',
		'Interfaces\\Front',
		'Interfaces\\Loader',
		'Interfaces\\Action',
		'Traits\\Action',
		'Interfaces\\Databases',
		'Interfaces\\Serializer',
		'Interfaces\\Common\\Registers',
		'Interfaces\\Controller',
		'Interfaces\\Helper',
		'Interfaces\\Model',
		'Interfaces\\Url',
		'Api\\Database',
		'Exception',
		'Render',
		'Debug',
		'RESTful',
		'Factory'
	];

	/**
	 * 
	 * @var string[]
	 */
	private static $legacy = [
		'Controller',
		'Model',
		'Document',
		'Captcha',
		'Caches',
		'Pagination',
		'Request',
		'Mail',
		'Translate',
		'Encryption'
	];

	function __construct() {
	}

	/**
	 * Load SPL Autoloader
	 * 
	 * @return bool 
	 * @throws \TypeError 
	 */
	static public function run() {
		return spl_autoload_register([self::class, 'load']);
	}

	/**
	 * 
	 * @return bool 
	 */
	static public function checkComposerLoader() {
		return self::$composerLoaded;
	}

	/**
	 * Resolve file path 
	 * 
	 * @param string $file 
	 * @return false|\SplFileInfo 
	 */
	static protected function fileResolver($file) {
		$splFile = new \SplFileInfo($file);
		if(!$splFile->isReadable()) return false;

		return $splFile;
	}

	/**
	 * 
	 * @param string|\SplFileInfo $file 
	 * @return bool|void 
	 */
	static protected function loadClassFile($file){
		$file = ($file instanceof \SplFileInfo) ? $file : self::fileResolver($file);

		if($file){
			if(self::required($file)) {
				self::getInstance()->loadedClasses[] = count(self::$subCallClass) > 1 ? self::$subCallClass :  self::$class;
				self::$subCallClass = [];
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * 
	 * @param \SplFileInfo $file 
	 * @return mixed 
	 */
	static public function required(\SplFileInfo $file) {
		return require_once($file->getPathname());
	}

	/**
	 * 
	 * @return string[] 
	 */
	static public function getAllowedSystemClasses() {
		return self::$allowed;
	}

	/**
	 * 
	 * @return \Phacil\Framework\AutoLoad 
	 */
	static public function getInstance() {

		if(!self::$instance)
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * 
	 * @return bool 
	 */
	private function loadConfigClass(){
		if(self::$class != self::CONFIG_CLASS) return false;

		$classNative = str_replace('phacil\\framework\\', '', strtolower(self::$class));

		$value = DIR_SYSTEM . str_replace('\\', "/", $classNative) . '/autoload.php';

		$value = self::fileResolver($value);

		if (file_exists($value)) {
			try {
				if (($value)) {
					self::loadClassFile($value);
					
					return true;
				} else {
					throw new \Exception("I can't load '$value' file! Please check system permissions.");
				}
			} catch (\Exception $e) {
				$log = new \Phacil\Framework\Log("exception.log");
				$log->write(self::$class . ' not loaded!');
				exit($e->getMessage());
			}
		}

		return false;
	}

	/** @return bool  */
	public function checkConfigLoaded(){
		return isset($this->loadedClasses[0]) && $this->loadedClasses[0] == self::CONFIG_CLASS;
	}

	/**
	 * @return true|void 
	 * @throws \Exception 
	 */
	protected function configIsFirst(){
		try {
			if(!$this->checkConfigLoaded()) throw new \Exception("Config need to be a first class loaded!");

			return true;
		} catch (\Exception $th) {
			throw $th;
		}
	}

	/**
	 * 
	 * @return void 
	 */
	private function prepareNamespaces() {
		self::$namespace = explode(self::SEPARATOR, self::$class);
		self::$namespaceWithoutPrefix = (\Phacil\Framework\Config::NAMESPACE_PREFIX()) ? explode(self::SEPARATOR, str_replace(\Phacil\Framework\Config::NAMESPACE_PREFIX() . self::SEPARATOR, "", self::$class)) : self::$namespace;

		if(self::isPhacil() ) {
			$classPhacilOutput = array_slice(self::$namespace, 2); //2 sliced because the Phacil Namespace is Phacil\Framework
			self::$classNative = implode(self::SEPARATOR, $classPhacilOutput);

			return;
		}

		self::$classNative = self::$class;
	}

	/**
	 * 
	 * @return bool 
	 * @throws \Phacil\Framework\Exception 
	 */
	private function legacyLoad() {
		if (in_array(self::$class, self::$legacy)) {
			try {
				class_alias("\\Phacil\\Framework\\" . self::$class, self::$class);
				return true;
			} catch (\Exception $th) {
				throw new \Phacil\Framework\Exception($th->getMessage());
			}
			return false;
		}

		return false;
	}

	/**
	 * Check if class is an Framework Class
	 * 
	 * @return bool 
	 */
	static private function isPhacil() {
		return isset(self::$namespace[0]) && self::$namespace[0] == 'Phacil';
	}

	/**
	 * Load /system/engine classes
	 * 
	 * @return bool 
	 */
	private function loadEngineClasses() {
		if (!self::isPhacil()) return false;

		if (in_array(self::$classNative, self::$allowed)) {
			$file = \Phacil\Framework\Config::DIR_SYSTEM() . 'engine/' . str_replace(self::SEPARATOR, "/", strtolower(self::$classNative)) . '.php';
			
			try {
				if (!self::loadClassFile($file)) {
					$file = \Phacil\Framework\Config::DIR_SYSTEM() . 'engine/' . str_replace(self::SEPARATOR, "/", self::$classNative) . '.php';
					if (!self::loadClassFile($file)) {
						throw new \Exception("Class " . self::$class . " not loaded.");
					}
				}
				return true;
			} catch (\Exception $th) {
				$log = new \Phacil\Framework\Log("exception.log");
				$log->write($th->getMessage());
			}
		}

		return false;
	}

	/**
	 * Load \Phacil\Framework\Interfaces\Action implemented classes
	 * 
	 * @return bool 
	 */
	private function loadActionClasses() {
		if (!self::isPhacil()) return false;

		if(self::$class != 'Phacil\Framework\Action') return false;

		$file = (\Phacil\Framework\Config::DIR_SYSTEM() . 'engine/action.php');

		try {
			if (!self::loadClassFile($file)) {
				throw new \Exception("Action classes is not loaded.");
			}
			return true;
		} catch (\Exception $th) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($th->getMessage());
		}

		return false;
	}

	/**
	 * Load database classes
	 * 
	 * @return bool
	 * @throws \Phacil\Framework\Exception 
	 */
	private function loadDatabase() {
		if(!self::isPhacil()) return false;

		if (isset(self::$namespace[2]) && self::$namespace[2] == 'Databases') {

			$fileDB = \Phacil\Framework\Config::DIR_DATABASE(\Phacil\Framework\Config::DIR_SYSTEM() . "database/") . str_replace(self::SEPARATOR, "/", strtolower(self::$classNative)) . '.php';

			//$fileDB = self::fileResolver($fileDB);

			try {
				if (!self::loadClassFile($fileDB)) {
					$fileDB = \Phacil\Framework\Config::DIR_DATABASE(\Phacil\Framework\Config::DIR_SYSTEM() . "database/") . str_replace(self::SEPARATOR, "/", self::$classNative) . '.php';
					if (!self::loadClassFile($fileDB)) {
						return false; //throw new \Phacil\Framework\Exception($fileDB . ' does not exist', 2);
					} 
				} 

				return true;
			} catch (Exception $th) {
				throw new \Phacil\Framework\Exception($th->getMessage(), $th->getCode(), $th);
			}

			
		}
		
		return false;
	}

	/**
	 * Try to load an framework class with autoload
	 * 
	 * @return bool 
	 */
	private function loadEngineAutoload() {
		if (!self::isPhacil()) return false;
		
		try {
			$value = \Phacil\Framework\Config::DIR_SYSTEM() . str_replace('\\', "/", strtolower(self::$classNative)) . '/autoload.php';

			if (self::loadClassFile($value)) {
				return true;
			}

			$value2 = \Phacil\Framework\Config::DIR_SYSTEM() . str_replace('\\', "/", self::$classNative) . '/autoload.php';
			if (self::loadClassFile($value2)) {
				return true;
			} 
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write(self::$class . ' not loaded!');
			exit($e->getMessage());
		}

		return false;
		
	}

	/** 
	 * Try to load an framework class without autoload
	 * 
	 * @return bool  
	 */
	private function loadEngineAutoload2() {
		if (!self::isPhacil()) return false;
		
		try {
			$value = \Phacil\Framework\Config::DIR_SYSTEM() . str_replace('\\', "/", strtolower(self::$classNative)) . '.php';
			if (self::loadClassFile($value)) {
				return true;
			}

			$value2 = \Phacil\Framework\Config::DIR_SYSTEM() . str_replace('\\', "/", self::$classNative) . '.php';
			if (self::loadClassFile($value2)) {
				return true;
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write(self::$class . ' not loaded!');
			exit($e->getMessage());
		}

		return false;
	}

	/**
	 * Try load modular classes 
	 * 
	 * @return bool 
	 */
	private function loadModularFiles() {
		$modulesPrepared = array_map(function ($item) {
			return \Phacil\Framework\Registry::case_insensitive_pattern($item);
		}, self::$namespace);

		$tryMagicOne = \Phacil\Framework\Config::DIR_APP_MODULAR() . implode("/", $modulesPrepared) . ".php";

		$files = glob($tryMagicOne, GLOB_NOSORT);

		try {
			if (!empty($files) && self::loadClassFile($files[0])) {
				return true;
			} 
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($e->getMessage());
			exit($e->getMessage());
		}

		return false;
	}

	/**
	 * Try load modular class without namespace
	 * 
	 * @return bool 
	 */
	private function loadModularWithoutNamespacesPrefix() {
		$modulesPrepared = array_map(function ($item) {
			return \Phacil\Framework\Registry::case_insensitive_pattern($item);
		}, self::$namespaceWithoutPrefix);

		$tryMagicOne = \Phacil\Framework\Config::DIR_APP_MODULAR() . implode("/", $modulesPrepared) . ".php";

		$files = glob($tryMagicOne, GLOB_NOSORT);

		try {
			if (!empty($files) && self::loadClassFile($files[0])) {
				return true;
			} 
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($e->getMessage());
			exit($e->getMessage());
		}

		return false;
	}

	/** 
	 * Try load modular parent class
	 * 
	 * @return bool  
	 */
	private function loadModularNamespaceShift() {
		$namespace = self::$namespace;
		$prefix = array_shift($namespace);

		$tryMagicOne = \Phacil\Framework\Config::DIR_APP_MODULAR() . implode("/", $namespace) . ".php" ;
		try {
			if (self::loadClassFile($tryMagicOne)) {
				return true;
			} 
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($e->getMessage());
			exit($e->getMessage());
		}

		return false;
		
	}

	/** 
	 * Support for Factory class.
	 * Need only for PHP 5.6 to 7.1
	 * @return bool  
	 */
	private function setFactoryAliases(){
		$class = self::$class;
		if (substr($class, -7) === "Factory" && $class !== \Phacil\Framework\Registry::FACTORY_CLASS) {
			class_alias(\Phacil\Framework\Registry::FACTORY_CLASS, $class);
			return true;
		} 

		return false;
	}

	/**
	 * Load Composer
	 * 
	 * @return bool 
	 * @throws \Phacil\Framework\Exception 
	 */
	static public function loadComposer() {
		if (self::isPhacil() || self::checkComposerLoader()) return false;

		$composer = \Phacil\Framework\Config::DIR_VENDOR() ?: \Phacil\Framework\Config::DIR_VENDOR(\Phacil\Framework\Config::DIR_SYSTEM() . 'vendor/autoload.php');

		/**
		 * fix for Polyfill Mbstring in older PHP versions
		 */

		 $cMbstring  = false;

		if (version_compare(phpversion(), '7.0.0', '>') == false || extension_loaded('mbstring')) {
			$cMbstring = \Phacil\Framework\Compatibility\Polyfill\Mbstring::load();
		
			$GLOBALS['__composer_autoload_files']['0e6d7bf4a5811bfa5cf40c5ccd6fae6a'] = 'noLoad';
		}
		/**
		 * End fix
		 */

		if ($autoloadComposer = self::loadClassFile($composer)) {
			self::$composerLoaded = $autoloadComposer;
			
			return true;
		} else {
			$log = new \Phacil\Framework\Log("exception.log");
			$m = 'Composer load not found.';
			$log->write($m);
			throw new Exception($m);
		}

	}

	protected function checkIsLoadedClass($class) {
		return in_array($class, $this->loadedClasses);
	}

	/**
	 * Initite loaders process
	 * 
	 * @param string $class 
	 * @return void 
	 * @throws \Exception 
	 * @throws \Phacil\Framework\Exception 
	 */
	static public function load($class) {
		self::$class = $class;

		self::$subCallClass[] = $class;

		$autoload = self::getInstance();

		//if($autoload->checkIsLoadedClass($class)) return;

		if($autoload->loadConfigClass()) return;

		$autoload->configIsFirst();

		$autoload->prepareNamespaces();

		if($autoload->legacyLoad()) return;

		if($autoload->loadActionClasses()) return;
		
		if($autoload->loadDatabase()) return;

		if($autoload->loadEngineClasses()) return;

		if($autoload->loadEngineAutoload()) return;

		if($autoload->loadEngineAutoload2()) return;

		if($autoload->loadModularFiles()) return;

		if($autoload->loadModularWithoutNamespacesPrefix()) return;

		/* if (version_compare(phpversion(), "7.2.0", "<")) {
			if($autoload->setFactoryAliases()) return;
		} */

		//if($autoload->loadModularNamespaceShift()) return;

		//if($autoload->loadComposer()) return;
		
		return;
	}
 }

AutoLoad::run();

$config = \Phacil\Framework\Config::INIT();

AutoLoad::loadComposer();