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
	static public function getInstance($class = null, $args = [], $onlyCheckInstances = false) {
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

		if($onlyCheckInstances) return $return;

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


	public function injectionClass($class)
	{
		$refClass = new ReflectionClass($class);

		if (!$refClass->getConstructor()) {
			if ($refClass->hasMethod('getInstance') && $refClass->getMethod('getInstance')->isStatic()) {
				return $refClass->getMethod('getInstance')->invoke(null);
			}

			return $refClass->newInstanceWithoutConstructor();
		}

		try {
			if ( $autoInstance = $this->getInstance($class, [], true))
				return $autoInstance;
		} catch (\Throwable $th) {
			//throw $th;
		}


		$rMethod = new ReflectionMethod($class, "__construct");
		$params = $rMethod->getParameters();
		$argsToInject = [];
		foreach ($params as $param) {
			//$param is an instance of ReflectionParameter
			try {
				if (version_compare(phpversion(), "7.2.0", "<")) {
					if ($param->getClass()) {
						$injectionClass = $param->getClass()->name;
						if (class_exists($injectionClass)) {
							$argsToInject[$param->getPosition()] = $this->injectionClass($injectionClass);
							continue;
						}
						if (!$param->isOptional()) {
							throw new \Phacil\Framework\Exception\ReflectionException("Error Processing Request: " . $injectionClass . "not exist");
						}
					}
				} else {
					if ($param->getType()) {
						$injectionClass = $param->getType()->getName();
						if (class_exists($injectionClass)) {
							$argsToInject[$param->getPosition()] = $this->injectionClass($injectionClass);
							continue;
						}
						if (!$param->isOptional()) {
							throw new \Phacil\Framework\Exception\ReflectionException("Error Processing Request: " . $injectionClass . "not exist");
						}
					}
				}
			} catch (\Exception $th) {
				throw $th;
			}

			if ($param->isOptional() && $param->isDefaultValueAvailable()) {
				$argsToInject[] = $param->getDefaultValue();
				continue;
			}
			if ($param->isOptional()) {
				$argsToInject[] = null;
			}
		}

		return self::setAutoInstance($refClass->newInstanceArgs($argsToInject));
	}
}
