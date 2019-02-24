<?php
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
	
	public function model($model) {
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
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set(str_replace('/', '_', $database), new $class($hostname, $username, $password, $database));
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