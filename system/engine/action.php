<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Action {
	protected $file;
	protected $class;
	protected $method;
	protected $args = array();

	/**
	 * @param string $route 
	 * @param array $args 
	 * @return void 
	 */
	public function __construct($route, $args = array()) {
		$path = '';
		$pathC = "";
		
		$parts = explode('/', str_replace('../', '', (string)$route));
		
		foreach ($parts as $part) { 
			$pathNew = $path;
			$path .= $part;
			
			if (is_dir(DIR_APP_MODULAR . $path)) {
				$path = $path.'/';
				
				array_shift($parts);
				
				continue;
			}elseif (is_dir(DIR_APP_MODULAR . ucfirst($path))) {
				$path = ucfirst($path).'/';
				
				array_shift($parts);
				
				continue;
			}elseif (is_dir(DIR_APPLICATION . 'controller' . $path)) {
				$path .= '/';
				
				array_shift($parts);
				
				continue;
			}
			
			if (is_file(DIR_APP_MODULAR  . str_replace('../', '', $pathNew) . 'Controller/' . str_replace('../', '', $part) . '.php')) {
				$this->file = DIR_APP_MODULAR . str_replace('../', '', $pathNew) . 'Controller/' . str_replace('../', '', $part) . '.php';
				
				$this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				array_shift($parts);
				
				break;
			} elseif (is_file(DIR_APP_MODULAR  . str_replace('../', '', $pathNew) . 'Controller/' . str_replace('../', '', ucfirst($part)) . '.php')) {
				$this->file = DIR_APP_MODULAR . str_replace('../', '', $pathNew) . 'Controller/' . str_replace('../', '', ucfirst($part)) . '.php';
				
				$this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				array_shift($parts);
				
				break;
			} elseif (is_file(DIR_APPLICATION . 'controller/' . str_replace('../', '', $path) . '.php')) {
				$this->file = DIR_APPLICATION . 'controller/' . str_replace('../', '', $path) . '.php';
				
				$this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				array_shift($parts);
				
				break;
			}
		}
		
		if ($args) {
			$this->args = $args;
		}
			
		$method = array_shift($parts);
				
		if ($method) {
			$this->method = $method;
		} else {
			$this->method = 'index';
		}

	}
	
	/** @return string  */
	public function getFile() {
		return $this->file;
	}
	
	/** @return string  */
	public function getClass() {
		return $this->class;
	}
	
	/** @return string  */
	public function getMethod() {
		return $this->method;
	}
	
	/** @return array  */
	public function getArgs() {
		return $this->args;
	}
}


/** @package Phacil\Framework */
final class ActionSystem {
	protected $file;
	protected $class;
	protected $method;
	protected $args = array();

	/**
	 * @param string $route 
	 * @param array $args 
	 * @return void 
	 */
	public function __construct($route, $args = array()) {
		$path = '';
		
		$parts = explode('/', str_replace('../', '', (string)$route));
		
		foreach ($parts as $part) { 
			$path .= $part;
			
			if (is_dir(DIR_SYSTEM . '' . $path)) {
				$path .= '/';
				
				array_shift($parts);
				
				continue;
			}
			
			if (is_file(DIR_SYSTEM . '' . str_replace('../', '', $path) . '.php')) {
				$this->file = DIR_SYSTEM . '' . str_replace('../', '', $path) . '.php';
				
				$this->class = 'System' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				array_shift($parts);
				
				break;
			}
		}
		
		if ($args) {
			$this->args = $args;
		}
			
		$method = array_shift($parts);
				
		if ($method) {
			$this->method = $method;
		} else {
			$this->method = 'index';
		}
	}
	
	/** @return string  */
	public function getFile() {
		return $this->file;
	}
	
	/** @return string  */
	public function getClass() {
		return $this->class;
	}
	
	/** @return string  */
	public function getMethod() {
		return $this->method;
	}
	
	/** @return array  */
	public function getArgs() {
		return $this->args;
	}
}
