<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework;

final class Loader {
	protected $registry;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	public function library($library) {
		$file = DIR_SYSTEM . 'library/' . $library . '.php';
		
		if (file_exists($file)) {
			return include_once($file);
		} else {
			return trigger_error('Error: Could not load library ' . $library . '!');
			exit();					
		}
	}
	
	public function model(string $model) {

		$parts = explode('/', str_replace('../', '', (string)$model));

		$lastPart = array_pop($parts);

		$path = str_replace('../', '', implode("/", $parts) );

		$file = DIR_APP_MODULAR.$path."/model/". $lastPart.".php";

		if(file_exists($file)){
			include_once($file);
			$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
			
			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {

			$file  = DIR_APPLICATION . 'model/' . $model . '.php';
			$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
			
			if (file_exists($file)) {
				include_once($file);
				
				$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
			} else {
				trigger_error('Error: Could not load model ' . $model . '!');
				exit();					
			}
		}
		
	}
	
	public function helper(string $helper) {

		$parts = explode('/', str_replace('../', '', (string)$helper));

		$lastPart = array_pop($parts);

		$path = str_replace('../', '', implode("/", $parts) );

		$file = DIR_APP_MODULAR.$path."/helper/". $lastPart.".php";

		if(file_exists($file)){
			include_once($file);
			/* $class = 'Helper' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
			
			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry)); */
		} else {
			trigger_error('Error: Could not load Helper ' . $helper . '!');
			exit();					
		}
		
	}

    public function control($model) { //temp alias, consider change to loader controller function
	    $this->controller($model);
    }

	public function controller($control) {
		$file  = DIR_APPLICATION . 'controller/' . $control . '.php';
		$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $control);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set('controller_' . str_replace('/', '_', $control), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $control . '!');
			exit();					
		}
	}
	 
	public function database($driver, $hostname, $username, $password, $database, $port = NULL, $charset = NULL) {
		$file  = DIR_SYSTEM . 'database/database/' . $driver . '.php';
		$class = ($driver);

		$replace = [
		    '/' => '_',
            '.' => '_'
        ];

        $database_name = str_replace(array_keys($replace), array_values($replace), preg_replace('/[^a-zA-Z0-9]/', '', $database));

		
		if (file_exists($file)) {
            //include_once($file);

            $this->db->createSubBase($database_name, new DB($driver, $hostname, $username, $password, $database));

			return $database_name;
		} else {
			trigger_error('Error: Could not load database ' . $driver . '!');
			exit();				
		}
	}
	
	public function config($config) {
		$this->config->load($config);
	}
	
	public function language($language) {
		return $this->language->load($language);
	}
} 
?>