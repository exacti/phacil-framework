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

	private $classAlt = [];

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

			$strReplaceOnPathNew = str_replace('../', '', $pathNew);
			$strReplaceOnPath = str_replace('../', '', $path);
			$strReplaceOnPart = str_replace('../', '', $part);
			$pregReplaceOnPath = preg_replace('/[^a-zA-Z0-9]/', '', $path);
			$pregReplaceOnPart = preg_replace('/[^a-zA-Z0-9]/', '', $part);
			
			if (is_file(DIR_APP_MODULAR  . $strReplaceOnPathNew  . 'Controller/' . $strReplaceOnPart . '.php')) {
				$this->file = DIR_APP_MODULAR . $strReplaceOnPathNew  . 'Controller/' . $strReplaceOnPart . '.php';
				
				$this->class = 'Controller' . $pregReplaceOnPath;

				$this->classAlt = [
					'class' => $this->mountClass($strReplaceOnPathNew, $pregReplaceOnPart),
					'legacy' => $this->class,
					'ucfirst' => ucfirst($pregReplaceOnPart),
					'direct' => $pregReplaceOnPart
				];

				array_shift($parts);
				
				break;
			} elseif (is_file(DIR_APP_MODULAR  . $strReplaceOnPathNew  . 'Controller/' . ucfirst($strReplaceOnPart) . '.php')) {
				$this->file = DIR_APP_MODULAR . $strReplaceOnPathNew  . 'Controller/' . ucfirst($strReplaceOnPart) . '.php';
				
				$this->class = 'Controller' . $pregReplaceOnPath;

				$this->classAlt = [
					'class' => $this->mountClass($strReplaceOnPathNew, $pregReplaceOnPart),
					'legacy' => $this->class,
					'ucfirst' => ucfirst($pregReplaceOnPart),
					'direct' => $pregReplaceOnPart
				];

				array_shift($parts);
				
				break;
			} elseif (is_file(DIR_APPLICATION . 'controller/' . $strReplaceOnPath . '.php')) {
				$this->file = DIR_APPLICATION . 'controller/' . $strReplaceOnPath . '.php';
				
				$this->class = 'Controller' . $pregReplaceOnPath;

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

	private function mountClass(string $namespace, string $class) {
		return (defined('NAMESPACE_PREFIX') ? NAMESPACE_PREFIX."\\" : "").str_replace("/", "\\", $namespace)."Controller\\".$class;
	}

	/**
	 * 
	 * @param string $class 
	 * @return $this 
	 */
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}

	/** @return array  */
	public function getClassAlt() {
		return $this->classAlt;
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
	 * 
	 * @var (string[]|string|null)[]
	 */
	private $classAlt = [];

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

				$this->classAlt = [
					'legacy' => $this->class,
					'direct' => preg_replace('/[^a-zA-Z0-9]/', '', $part)
				];

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
	
	/** @return array  */
	public function getClassAlt() {
		return $this->classAlt;
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
