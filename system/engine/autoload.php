<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


/** @autoload class */
spl_autoload_register(function ($class) {
	$namespace = explode("\\", $class);
	$namespaceWithoutPrefix = (defined('NAMESPACE_PREFIX')) ? explode("\\", str_replace(NAMESPACE_PREFIX."\\" , "", $class)) : $namespace;

	$legacy = [
		'Controller',
		'Model',
		'Document',
		'Captcha',
		'Caches',
		'Pagination',
		'Request',
		'Mail',
		'Translate'
	];

	if(in_array($class, $legacy)){
		try {
			class_alias("\\Phacil\\Framework\\".$class, $class);
		} catch (\Exception $th) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($class.' not loaded!');
		}
		
		//eval("class ".$class." extends \\Phacil\\Framework\\".$class." {}");
		return;
	}

	$classNative = ($namespace[0] == "Phacil") ? str_replace('phacil\\framework\\', '', strtolower( $class)) : $class;

	if($namespace[0] == 'Phacil' && isset($namespace[2]) && $namespace[2] == 'Databases'){
		if(!defined('DIR_DATABASE'))
    		define('DIR_DATABASE', DIR_SYSTEM."database/");


		$fileDB = DIR_DATABASE . str_replace("\\", "/", $classNative).'.php';

		try {
			if (!file_exists($fileDB))
				throw new Exception ($fileDB.' does not exist');
			else
				require_once($fileDB);

			return;
		} catch (Exception $th) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($th->getMessage());
			
		}
	}

	$allowed = [
		'log',
		'front',
		'controller',
		'loader',
		'model',
		'registry',
		'document',
		'response',
		'classes',
		'abstracthelper',
		'interfaces\\front',
		'interfaces\\loader',
		'interfaces\\action',
		'traits\\action',
		'interfaces\\databases'
	];

	if($namespace[0] == "Phacil" && in_array($classNative, $allowed)){
		$file = DIR_SYSTEM . 'engine/'. str_replace("\\", "/", $classNative).'.php';

		try {
			if(is_readable($file)){
				require_once($file);
				return;
			} else {
				throw new \Exception("Class '$class' not loaded.");
			}
		} catch (\Exception $th) {
			
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($th->getMessage());
			
		}
	}

	$value = DIR_SYSTEM . $classNative.'/autoload.php';

	if($namespace[0] == "Phacil" && in_array($value, $this->dirs)){
		try {
			if(is_readable($value)) {
				require_once $value;
				return;
			} else {
				throw new \Exception("I can't load '$value' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($class.' not loaded!');
			exit($e->getMessage());
		}
	}

	
	if(file_exists($tryMagicOne = DIR_APP_MODULAR. implode("/", $namespace).".php")){
		try {
			if(is_readable($tryMagicOne)) {
				require_once $tryMagicOne;
				return;
			} else {
				throw new \Exception("I can't load '$tryMagicOne' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($e->getMessage());
			exit($e->getMessage());
		}
	} 
	

	if(file_exists($tryMagicOne = DIR_APP_MODULAR. implode("/", $namespaceWithoutPrefix).".php")){
		try {
			if(is_readable($tryMagicOne)) {
				require_once $tryMagicOne;
				return;
			} else {
				throw new \Exception("I can't load '$tryMagicOne' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($e->getMessage());
			exit($e->getMessage());
		}
	} 

	$prefix = array_shift($namespace);
	
	if(file_exists($tryMagicOne = DIR_APP_MODULAR. implode("/", $namespace).".php")){
		try {
			if(is_readable($tryMagicOne)) {
				require_once $tryMagicOne;
				return;
			} else {
				throw new \Exception("I can't load '$tryMagicOne' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($e->getMessage());
			exit($e->getMessage());
		}
	}

	return;
		
});

require_once(DIR_SYSTEM . 'engine/action.php'); 