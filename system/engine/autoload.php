<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


require_once(DIR_SYSTEM . 'engine/action.php'); 
require_once(DIR_SYSTEM . 'engine/controller.php');


spl_autoload_register(function ($class) {
	$namespace = explode("\\", $class);

	var_dump($class);

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

		class_alias("\\Phacil\\Framework\\".$class, $class);
		//eval("class ".$class." extends \\Phacil\\Framework\\".$class." {}");
		return;
	}

	$class = ($namespace[0] == "Phacil") ? str_replace('phacil\\framework\\', '', strtolower( $class)) : $class;

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
		'abstracthelper'
	];

	if($namespace[0] == "Phacil" && in_array($class, $allowed)){
		try {
			include_once(DIR_SYSTEM . 'engine/'. $class.'.php');
			return;
		} catch (\Throwable $th) {
			throw new \Exception("Class '$class' not loaded.");
		}
	}

	$value = DIR_SYSTEM . $class.'/autoload.php';

	if($namespace[0] == "Phacil" && in_array($value, $this->dirs)){
		try {
			if(is_readable($value)) {
				require_once $value;
				return;
			} else {
				throw new \Exception("I can't load '$value' file! Please check system permissions.");
			}
		} catch (\Exception $e) {
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
			exit($e->getMessage());
		}
	}
	return;
		
});


//require_once(DIR_SYSTEM . 'engine/legacy.php');