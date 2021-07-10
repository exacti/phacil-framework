<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

//require_once(DIR_SYSTEM . 'engine/log.php');
require_once(DIR_SYSTEM . 'engine/action.php'); 
require_once(DIR_SYSTEM . 'engine/controller.php');
/* require_once(DIR_SYSTEM . 'engine/front.php');
require_once(DIR_SYSTEM . 'engine/loader.php'); 
require_once(DIR_SYSTEM . 'engine/model.php');
require_once(DIR_SYSTEM . 'engine/registry.php');
require_once(DIR_SYSTEM . 'engine/document.php');
require_once(DIR_SYSTEM . 'engine/response.php');
require_once(DIR_SYSTEM . 'engine/classes.php'); */
//require_once(DIR_SYSTEM . 'engine/caches.php');


spl_autoload_register(function ($class) {
	$namespace = explode("\\", $class);

	$class = str_replace('phacil\framework\\', '', strtolower( $class));

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
		//'caches'
	];

	if($namespace[0] == "Phacil" && in_array($class, $allowed)){
		try {
			include_once(DIR_SYSTEM . 'engine/'. $class.'.php');
		} catch (\Throwable $th) {
			throw new \Exception("Class not load");
		}
	}
		
});


require_once(DIR_SYSTEM . 'engine/legacy.php');