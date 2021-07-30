<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


require_once(DIR_SYSTEM . 'engine/action.php'); 
require_once(DIR_SYSTEM . 'engine/controller.php');

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
			$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
			$log->write($class.' not loaded!');
		}
		
		//eval("class ".$class." extends \\Phacil\\Framework\\".$class." {}");
		return;
	}

	$classNative = ($namespace[0] == "Phacil") ? str_replace('phacil\\framework\\', '', strtolower( $class)) : $class;

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
		'interfaces\front'
	];

	if($namespace[0] == "Phacil" && in_array($classNative, $allowed)){
		try {
			include_once(DIR_SYSTEM . 'engine/'. str_replace("\\", "/", $classNative).'.php');
			return;
		} catch (\Exception $th) {
			$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
			$log->write($class.' not loaded!');
			throw new \Exception("Class '$class' not loaded.");
		}
	}

	$value = DIR_SYSTEM . $classNative.'/autoload.php';

	if($namespace[0] == "Phacil" && in_array($value, $this->dirs)){
		try {
			if(is_readable($value)) {
				require_once $value;
				return;
			} else {
				$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
				$log->write($class.' not loaded!');
				throw new \Exception("I can't load '$value' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
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
				$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
				$log->write($class.' not loaded!');
				throw new \Exception("I can't load '$tryMagicOne' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
			$log->write($class.' not loaded!');
			exit($e->getMessage());
		}
	} 
	

	if(file_exists($tryMagicOne = DIR_APP_MODULAR. implode("/", $namespaceWithoutPrefix).".php")){
		try {
			if(is_readable($tryMagicOne)) {
				require_once $tryMagicOne;
				return;
			} else {
				$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
				$log->write($class.' not loaded!');
				throw new \Exception("I can't load '$tryMagicOne' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
			$log->write($class.' not loaded!');
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
				$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
				$log->write($class.' not loaded!');
				throw new \Exception("I can't load '$tryMagicOne' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log(DIR_LOGS."exception.log");
			$log->write($class.' not loaded!');
			exit($e->getMessage());
		}
	}

	return;
		
});


//require_once(DIR_SYSTEM . 'engine/legacy.php');