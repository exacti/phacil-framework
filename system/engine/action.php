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
class Action implements ActionInterface {

	use ActionTrait;

	/**
	 * @inheritdoc
	 */
	public function __construct($route, $args = array(), $local = self::APP) {
		if (strlen($route) > self::MAX_ROUTE_LENGTH) {
			$route = substr($route, 0, self::MAX_ROUTE_LENGTH);
		}
		if($local == self::APP) {
			$this->normal($route, $args);
		}
		if($local == self::SYSTEM) {
			$this->system($route, $args);
		}
	}

	/**
	 * @param string $route 
	 * @param array $args 
	 * @return void 
	 */
	private function system($route, $args = array())
	{
		$path = '';

		$parts = explode('/', str_replace('../', '', (string)$route));
		$parts = array_map(function ($part) {
			return preg_replace('/[^a-zA-Z0-9_]/', '', $part);
		}, $parts);

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

	/**
	 * @param string $route 
	 * @param array $args 
	 * @return void 
	 */
	private function normal($route, $args = array()) {
		$parts = explode('/', str_replace('../', '', (string)$route));

		$parts = array_map(function($part){
			return preg_replace('/[^a-zA-Z0-9_]/', '', $part);
		}, $parts);

		$this->route = $route;
		$testClass = $this->ClassPossibilities($parts, 3);

		if ($args) {
			$this->args = $args;
		}

		if(!empty($testClass)){
			$this->classAlt = [
				'class' => $testClass['class']
			];
			$this->method = $testClass['method'];
			return;
		}

		$testLegacyClass = $this->getLegacyController($parts);

		if(!empty($testLegacyClass)){
			$this->file = $testLegacyClass['file'];

			$this->class = $testLegacyClass['class'];

			$this->classAlt = [
				'class' => $testLegacyClass['class'],
				'legacy' => $this->class,
				'ucfirst' => ucfirst($testLegacyClass['class']),
				'direct' => $testLegacyClass['class']
			];

			$this->method = $testLegacyClass['method'];
			return;
		}

	}

	/**
	 * @param array $modules 
	 * @param int $position 
	 * @return array<array-key, string>[] 
	 */
	private function positionController(array $modules, $position = 2) {
		$posibleFiles = [];
		$modulesPrepared = array_map(function($item){
			return \Phacil\Framework\Registry::case_insensitive_pattern($item);
		}, $modules);
		$modulesWithoutLast = $modulesPrepared;
		array_pop($modulesWithoutLast);
		
		for ($i=1; $i < $position; $i++) {
			# code...
			$mount = $modulesPrepared;
			array_splice(($mount), $i, 0, 'Controller');
			$posibleFiles[] = $mount;
			$mount2 = $modulesWithoutLast;
			array_splice(($mount2), $i, 0, 'Controller');
			$posibleFiles[] = $mount2;
		}

		return $posibleFiles;
	}

	/**
	 * @param array $modules 
	 * @param int $position 
	 * @return array|string 
	 */
	private function getLegacyController(array $modules, $position = 0) {
		$possibleFile = null;
		$modulesPrepared = array_map(function ($item) {
			//return \Phacil\Framework\Registry::case_insensitive_pattern($item);
			return preg_replace('/[^a-zA-Z0-9_]/', '', $item);
		}, $modules);
		array_splice(($modulesPrepared), $position, 0, 'controller');
		$modulesWithoutLast = $modulesPrepared;
		array_pop($modulesWithoutLast);

		//$possibleFiles = glob(Config::DIR_APPLICATION().implode("/", $modulesPrepared).".php", GLOB_BRACE);
		$possibleFile = Config::DIR_APPLICATION() . implode("/", $modulesPrepared) . ".php";
		if(is_file($possibleFile)) {
			return [
				'class' => preg_replace('/[^a-zA-Z0-9]/', '', 'Controller'.implode("", $modules)),
				'file' => $possibleFile,
				'method' => 'index'
			];
		}

		//$possibleFiles = glob(Config::DIR_APPLICATION().implode("/", $modulesWithoutLast).".php", GLOB_BRACE);
		$possibleFile = Config::DIR_APPLICATION() . implode("/", $modulesWithoutLast) . ".php";
		if(is_file($possibleFile)) {
			$modulesTxtWithoutLast = $modules;
			array_pop($modulesTxtWithoutLast);
			return [
				'class' => preg_replace('/[^a-zA-Z0-9]/', '', 'Controller'.implode("", $modulesTxtWithoutLast)),
				'file' => $possibleFile,
				'method' => end($modules)
			];
		}

		return null;
	}

	/**
	 * @param array $modules 
	 * @param int $position 
	 * @return array 
	 */
	private function ClassPossibilities(array $modules, $position = 3) {
		$posibleClass = [];
		$modulesWithoutLast = $modules;
		$lastModule = array_pop($modulesWithoutLast);
		
		for ($i=1; $i < $position; $i++) {
			# code...
			if(Config::NAMESPACE_PREFIX()){
				$mount = $modules;
				array_splice($mount, $i, 0, 'Controller');
				array_unshift($mount, Config::NAMESPACE_PREFIX());
				if (class_exists($class = implode("\\", $mount))) {
					return ['class' => $class, 'method' => 'index'];
				}
			}

			$mount2 = $modules;
			array_splice($mount2, $i, 0, 'Controller');
			if(class_exists($class = implode("\\", $mount2))) {
				return ['class' => $class, 'method' => 'index'];
			}
			//$posibleFiles[] = $mount;
			$mount3 = $modulesWithoutLast;
			array_splice($mount3, $i, 0, 'Controller');
			//$posibleFiles[] = $mount2;

			if (class_exists($class = implode("\\", $mount3))) {
				return ['class' => $class, 'method' => $lastModule];
			}
		}

		return $posibleClass;
	}
	
}
