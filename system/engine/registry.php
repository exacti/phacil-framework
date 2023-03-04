<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework;
 
/** 
 * The registration off all objects on this Framework.
 * 
 * @since 0.0.1
 * 
 * @package Phacil\Framework 
 */
final class Registry {
	/**
	 * data Objects
	 * @var array
	 */
	private $data = array();

	/**
	 * Instances objects
	 * 
	 * @var array
	 */
	private $instances = [];

	/**
	 * Original route for childs
	 * @var string
	 */
	public $routeOrig;

	/**
	 * 
	 * @var string
	 */
	public $route;

	/**
	 * AutoInstances Loaded
	 * 
	 * @var array
	 */
	static private $autoInstances = [];

	/**
	 * Magic method to return engine instances
	 * 
	 * @param string $key 
	 * @return mixed 
	 */
	public function __get($key) {
		return $this->get($key);
	}

	/**
	 * @param string $key 
	 * @return mixed 
	 */
	public function get($key) {

		return (isset($this->instances[$key]) ? $this->instances[$key] : $this->engine->checkRegistry($key));
	}

	/**
	 * @param string $key 
	 * @param mixed $value 
	 * @return void 
	 */
	public function set($key, $value) {
		$this->instances[$key] = $value;
	}

	/**
	 * @param string $key 
	 * @return bool 
	 */
	public function has($key) {
    	return isset($this->instances[$key]);
  	}

	/**
	 * UnSet
	 *
	 * Unsets registry value by key.
	 *
	 * @param string $key
	 * @return void
	 */
	public function delete(string $key) {
		if (isset($this->instances[$key])) {
			unset($this->instances[$key]);
		}
	}

	/** 
	 * Try to obtain an iniciated engine instance
	 * 
	 * @param string|object|null $class (optional)
	 * @param array $args (optional)
	 * 
	 * @return \Phacil\Framework\Registry
	 * @since 2.0.0
	 */
	static public function getInstance($class = null, $args = []) {
		if(!$class) return \Phacil\Framework\startEngineExacTI::getRegistry();

		$registry = \Phacil\Framework\startEngineExacTI::getRegistry();

		$return = false;

		$classObj = (is_object($class)) ? get_class($class) : $class;

		if (isset(self::$autoInstances[($classObj)])) return self::$autoInstances[($classObj)];

		foreach ($registry->instances as $key => $value) {
			# code...
			if(!is_object($value)) continue;
			
			if(get_class($value) == $classObj) {$return = $value; break; }
		}

		if($return) return $return;

		if(is_string($class)) {
			$reflector = new ReflectionClass($class);
			return self::setAutoInstance($reflector->newInstanceArgs($args));
		}

		if (is_object($class)) {
			return self::getInstance($class);
		}

		return null;
	}

	/**
	 * 
	 * @param object $class 
	 * @return object 
	 * @throws \Phacil\Framework\Exception 
	 * @since 2.0.0
	 */
	static public function setAutoInstance($class) {
		if(!is_object($class)) throw new Exception('Object type is required!');

		self::$autoInstances[get_class($class)] = $class;
		return $class;
	}

	/**
	 * 
	 * @param object $class 
	 * @return object 
	 * @since 2.0.0
	 * @throws \Phacil\Framework\Exception 
	 */
	static public function getAutoInstance($class) {
		if (!is_object($class)) throw new Exception('Object type is required!');

		return isset(self::$autoInstances[get_class($class)]) ? self::$autoInstances[get_class($class)] : self::setAutoInstance($class);
	}
}
