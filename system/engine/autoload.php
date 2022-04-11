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
		'Translate'.
		'Encryption'
	];

	if(in_array($class, $legacy)){
		try {
			class_alias("\\Phacil\\Framework\\".$class, $class);
		} catch (\Exception $th) {
			throw new \Phacil\Framework\Exception ($th->getMessage());
		}
		
		return;
	}

	$classNative = ($namespace[0] == "Phacil") ? str_replace('phacil\\framework\\', '', strtolower( $class)) : $class;

	if($namespace[0] == 'Phacil' && isset($namespace[2]) && $namespace[2] == 'Databases'){

		$fileDB = \Phacil\Framework\Config::DIR_DATABASE(\Phacil\Framework\Config::DIR_SYSTEM() . "database/") . str_replace("\\", "/", $classNative).'.php';

		try {
			if (!file_exists($fileDB)){
				throw new \Phacil\Framework\Exception($fileDB.' does not exist', 2);
			}else{
				require_once($fileDB);

				return;
			}
		} catch (Exception $th) {
			throw new \Phacil\Framework\Exception($th->getMessage(), $th->getCode(), $th);
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
		'interfaces\\databases',
		'exception',
		'render',
		'debug'
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

	$value = DIR_SYSTEM . str_replace('\\', "/", $classNative) .'/autoload.php';

	if($namespace[0] == "Phacil" && file_exists($value)){
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

	$value = DIR_SYSTEM . str_replace('\\', "/", $classNative) . '.php';

	if ($namespace[0] == "Phacil" && file_exists($value) ) {
		try {
			if (is_readable($value)) {
				require_once $value;
				return;
			} else {
				throw new \Exception("I can't load '$value' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
			$log = new \Phacil\Framework\Log("exception.log");
			$log->write($class . ' not loaded!');
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

require_once(\Phacil\Framework\Config::DIR_SYSTEM() . 'engine/action.php');

$composer = \Phacil\Framework\Config::DIR_VENDOR() ?: \Phacil\Framework\Config::DIR_VENDOR(\Phacil\Framework\Config::DIR_SYSTEM() . 'vendor/autoload.php');

if (file_exists($composer)) {
	/**
	 * fix for Polyfill Mbstring in older PHP versions
	 */
	if (!function_exists('mb_convert_variables')) {
		function mb_convert_variables($toEncoding, $fromEncoding, &$a = null, &$b = null, &$c = null, &$d = null, &$e = null, &$f = null)
		{
			return Symfony\Polyfill\Mbstring\Mbstring::mb_convert_variables($toEncoding, $fromEncoding, $v0, $a, $b, $c, $d, $e, $f);
		}
	}
	if (extension_loaded('mbstring')) {
		$GLOBALS['__composer_autoload_files']['0e6d7bf4a5811bfa5cf40c5ccd6fae6a'] = 'noLoad';
	}
	/**
	 * End fix
	 */

	$autoloadComposer = (include_once $composer);
	//return;
}