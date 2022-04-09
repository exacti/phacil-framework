<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use \Phacil\Framework\Interfaces\Action as ActionInterface;
use \Phacil\Framework\Traits\Action as ActionTrait;
use \Phacil\Framework\Config;

/** 
 * Action class to route all user controllers
 * 
 * @since 1.0.0
 * 
 * @package Phacil\Framework 
 **/
final class Action implements ActionInterface {

	use ActionTrait;
	
	/**
	 * @inheritdoc
	 */
	public function __construct($route, $args = array()) {
		$path = '';
		$pathC = "";
		
		$parts = explode('/', str_replace('../', '', (string)$route));

		$this->route = $route;
		
		foreach ($parts as $part) { 
			$pathNew = $path;
			$path .= $part;
			
			if (is_dir(Config::DIR_APP_MODULAR() . $path)) {
				$path = $path.'/';
				
				array_shift($parts);
				
				continue;
			}elseif (is_dir(Config::DIR_APP_MODULAR() . ucfirst($path))) {
				$path = ucfirst($path).'/';
				
				array_shift($parts);
				
				continue;
			}elseif (is_dir(Config::DIR_APPLICATION() . 'controller/' . $path)) {
				$path .= '/';
				
				array_shift($parts);
				
				continue;
			}

			$strReplaceOnPathNew = str_replace('../', '', $pathNew);
			$strReplaceOnPath = str_replace('../', '', $path);
			$strReplaceOnPart = str_replace('../', '', $part);
			$pregReplaceOnPath = preg_replace('/[^a-zA-Z0-9]/', '', $path);
			$pregReplaceOnPart = preg_replace('/[^a-zA-Z0-9]/', '', $part);
			
			if (is_file(Config::DIR_APP_MODULAR()  . $strReplaceOnPathNew  . 'Controller/' . $strReplaceOnPart . '.php')) {
				$this->file = Config::DIR_APP_MODULAR() . $strReplaceOnPathNew  . 'Controller/' . $strReplaceOnPart . '.php';
				
				$this->class = 'Controller' . $pregReplaceOnPath;

				$this->classAlt = [
					'class' => $this->mountClass($strReplaceOnPathNew, $pregReplaceOnPart),
					'legacy' => $this->class,
					'ucfirst' => ucfirst($pregReplaceOnPart),
					'direct' => $pregReplaceOnPart
				];

				array_shift($parts);
				
				break;
			} elseif (is_file(Config::DIR_APP_MODULAR()  . $strReplaceOnPathNew  . 'Controller/' . ucfirst($strReplaceOnPart) . '.php')) {
				$this->file = Config::DIR_APP_MODULAR() . $strReplaceOnPathNew  . 'Controller/' . ucfirst($strReplaceOnPart) . '.php';
				
				$this->class = 'Controller' . $pregReplaceOnPath;

				$this->classAlt = [
					'class' => $this->mountClass($strReplaceOnPathNew, $pregReplaceOnPart),
					'legacy' => $this->class,
					'ucfirst' => ucfirst($pregReplaceOnPart),
					'direct' => $pregReplaceOnPart
				];

				array_shift($parts);
				
				break;
			} elseif (is_file(Config::DIR_APPLICATION() . 'controller/' . $strReplaceOnPath . '.php')) {
				$this->file = Config::DIR_APPLICATION() . 'controller/' . $strReplaceOnPath . '.php';
				
				$this->class = 'Controller' . $pregReplaceOnPath;

				$this->classAlt = [
					'class' => $this->mountClass($strReplaceOnPathNew, $pregReplaceOnPart),
					'legacy' => $this->class,
					'ucfirst' => ucfirst($pregReplaceOnPart),
					'direct' => $pregReplaceOnPart
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
	
	
}


/** 
 * Action class to route all framework system controllers
 * 
 * @since 1.0.1
 * 
 * @package Phacil\Framework */
final class ActionSystem implements ActionInterface {
	
	use ActionTrait;

	/**
	 * @inheritdoc
	 */
	public function __construct($route, $args = array()) {
		$path = '';
		
		$parts = explode('/', str_replace('../', '', (string)$route));
		
		foreach ($parts as $part) { 
			$path .= $part;
			
			if (is_dir(Config::DIR_SYSTEM() . '' . $path)) {
				$path .= '/';
				
				array_shift($parts);
				
				continue;
			}
			
			if (is_file(Config::DIR_SYSTEM() . '' . str_replace('../', '', $path) . '.php')) {
				$this->file = Config::DIR_SYSTEM() . '' . str_replace('../', '', $path) . '.php';
				
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
	
}
