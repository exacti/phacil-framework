<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework;
 
/** 
 * The registration of all objects on this Framework.
 * 
 * @since 0.0.1
 * 
 * @package Phacil\Framework 
 */
final class Registry {

	const FACTORY_CLASS = 'Phacil\Framework\Factory';

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
	 * AutoInstances Loaded
	 * 
	 * @var array
	 */
	static private $preferences = [];

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
			if(!is_object($value)) continue;
			
			if(get_class($value) == $classObj) {$return = $value; break; }
		}

		if($return) return $return;

		if($onlyCheckInstances) return $return;

		if(is_string($class)) {
			$classCreate = self::checkPreference($class);
			$reflector = new ReflectionClass($classCreate);
			return self::setAutoInstance($reflector->newInstanceArgs($args), $class);
		}

		if (is_object($class)) {
			return self::getInstance($class);
		}

		return null;
	}

	/**
	 * @param string $class 
	 * @param array $args 
	 * @return mixed 
	 * @throws \ReflectionException 
	 * @throws \Exception 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function create($class, $args = array()) {
		if(is_string($class)) {
			return $this->injectionClass($class, $args, true);
			/* $classCreate = self::checkPreference($class);
			$reflector = new ReflectionClass($classCreate);
			return self::setAutoInstance($reflector->newInstanceArgs($args), $class); */
		}
		return null;
	}

	/**
	 * Adds DI preferences from a JSON file.
	 *
	 * This method reads a JSON file containing preferences and merges them into the existing preferences array.
	 *
	 * @param string $jsonFilePath The path to the JSON file containing preferences.
	 * @return void 
	 * @throws \Phacil\Framework\Exception If there is an error reading the JSON file or if the JSON data is invalid.
	 */
	static public function addPreference($jsonFilePath) {
		if (file_exists($jsonFilePath)) {
			$jsonData = file_get_contents($jsonFilePath);

			$dataArray = json_decode($jsonData, true);

			// Verifica se a conversão foi bem-sucedida
			if ($dataArray === null && json_last_error() !== JSON_ERROR_NONE) {
				// Se houver um erro na conversão, trata o erro
				$error = new \Phacil\Framework\Exception(json_last_error_msg());
				return;
			}
		}
		if (isset($dataArray['preferences']))
			self::$preferences = array_merge(self::$preferences, $dataArray['preferences']);
	}
	
	/**
	 * Adds DI preferences from a JSON file.
	 *
	 * This method reads a JSON file containing preferences and merges them into the existing preferences array.
	 *
	 * @param string $jsonFilePath The path to the JSON file containing preferences.
	 * @return void 
	 * @throws \Phacil\Framework\Exception If there is an error reading the JSON file or if the JSON data is invalid.
	 */
	static public function addPreferenceByRoute($route) {
		$routeArray = explode('/', $route);

		$directory = \Phacil\Framework\Config::DIR_APP_MODULAR() . self::case_insensitive_pattern($routeArray[0]) . '/etc/preferences.json';

		$pattern = self::case_insensitive_pattern($routeArray[0]);

		$files = glob($directory . "*", GLOB_MARK);

		if(isset($files[0])) {
			$jsonFilePath = $files[0];
		} else {
			return;
		}
		if (file_exists($jsonFilePath)) {
			self::addPreference($jsonFilePath);
		}
	}

	/**
	 * Generate Glob case insensitive pattern
	 * @param string $string 
	 * @return string 
	 */
	static public function case_insensitive_pattern($string)
	{
		$pattern = '';
		foreach (str_split($string) as $char) {
			if (ctype_alpha($char)) { // se o caractere é uma letra
				$pattern .= '[' . strtoupper($char) . strtolower($char) . ']'; // adiciona as duas variações de caixa
			} else {
				$pattern .= $char; // mantém o caractere original
			}
		}
		return $pattern;
	}

	/**
	 * Checks if a preference has been set for the specified class and returns it if found.
	 *
	 * This method checks if a preference has been set for the specified class. If a preference
	 * is found, it returns the class name defined as the preference; otherwise, it returns the
	 * original class name.
	 *
	 * @param string $class The class name to check for a preference.
	 * @return string The class name defined as the preference, if found; otherwise, the original class name.
	 */
	static public function checkPreference($class) {
		if(isset(self::$preferences[$class])) {
			return self::$preferences[$class];
		}
		return $class;
	}

	/**
	 * Sets an auto-instantiated object to be used as a dependency.
	 *
	 * This method allows you to set an object to be automatically instantiated and used as a dependency
	 * when resolving other dependencies that require an instance of the specified class.
	 *
	 * @param object $class The object instance to be set as an auto-instantiated dependency.
	 * @param string|null (Optional) $original The original class name of the object being set.
	 * @return object The object instance that was set as an auto-instantiated dependency.
	 * @throws \Phacil\Framework\Exception If the provided parameter is not an object.
	 * @since 2.0.0
	 */
	static public function setAutoInstance($class, $original = null) {
		if(!is_object($class)) throw new Exception('Object type is required!');

		self::$autoInstances[$original ?: get_class($class)] = $class;
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


	/**
	 * Resolves and instantiates a class with optional constructor arguments and creation control.
	 *
	 * This method resolves a class by its name, instantiates it with optional constructor arguments,
	 * and provides control over the creation process.
	 * 
	 * @param string $class The fully qualified name of the class to instantiate.
	 * @param array $args (Optional) Arguments for the class constructor. Default is an empty array.
	 *                    If empty, constructor arguments will be automatically detected and injected.
	 * @param bool $forceCreate (Optional) Whether to force creation. Default is false.
	 * @return mixed 
	 * @throws \ReflectionException 
	 * @throws \Exception 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function injectionClass($class, $args = array(), $forceCreate = false, $factored = null)
	{
		$argsToInject = !empty($args) ? $args : false;
		$originalClass = $class;
		$class = self::checkPreference($class);
		$originalClass = $originalClass == $class ? null : $originalClass;
		$refClass = new ReflectionClass($class);

		try {
			if (!$forceCreate && $autoInstance = $this->getInstance($originalClass ?: $class, [], true))
				return $autoInstance;

			if (!$refClass->getConstructor() && !$argsToInject) {
				if ($refClass->hasMethod('getInstance') && $refClass->getMethod('getInstance')->isStatic()) {
					return $refClass->getMethod('getInstance')->invoke(null);
				}
				return $refClass->newInstanceWithoutConstructor();
			}

		} catch (\Exception $th) {
			//throw $th; Don't make anything and retry create

		}

		try {
			$rMethod = new ReflectionMethod($class, "__construct");

			if (!$argsToInject) {
				$params = $rMethod->getParameters();
				$argsToInject = [];
				foreach ($params as $param) {
					//$param is an instance of ReflectionParameter
					try {
						if (version_compare(phpversion(), "7.2.0", "<")) {
							if ($param->getClass()) {
								$injectionClass = $param->getClass()->name;

								if ($injectionClass === self::FACTORY_CLASS) {
									$declaringClass = $param->__toString();
									$pattern = '/<[^>]+>\s*([^$\s]+)/';

									if (preg_match($pattern, $declaringClass, $matches)) {
										$classFactoryName = $matches[1];
										if($classFactoryName !== $injectionClass){
											$factoredRefClass = substr($classFactoryName, 0, -7);
											
											$argsToInject[$param->getPosition()] = ($this->getInstance($classFactoryName, [], true)) ? : self::setAutoInstance($this->create(self::FACTORY_CLASS, [$factoredRefClass]), $classFactoryName);
											
											continue;
										}
									}
								}
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
								} elseif (substr($injectionClass, -7) === "Factory") {
									// Create a factored instance
									$factoredRefClass = substr($injectionClass, 0, -7);
									$argsToInject[$param->getPosition()] = ($this->getInstance($injectionClass, [], true)) ?: self::setAutoInstance($this->create(self::FACTORY_CLASS, [$factoredRefClass]), $injectionClass);
									class_alias(self::FACTORY_CLASS, $injectionClass);
									continue;
								}
								if (!$param->isOptional()) {
									throw new \Phacil\Framework\Exception\ReflectionException("Error Processing Request: " . $injectionClass . "not exist");
								}
							}
						}
					} catch (\ReflectionException $th) {
						throw new \Phacil\Framework\Exception\ReflectionException($th->getMessage(), $th->getCode(), $th);
					} catch (\Exception $th) {
						throw new \Phacil\Framework\Exception($th->getMessage());
					}

					if ($param->isOptional() && $param->isDefaultValueAvailable()) {
						$argsToInject[] = $param->getDefaultValue();
						continue;
					}
					if ($param->isOptional()) {
						$argsToInject[] = null;
					}
				}
			}
		} catch (\ReflectionException $th) {
			throw new \Phacil\Framework\Exception\ReflectionException($th->getMessage());
		} catch (\Exception $th) {
			throw new \Phacil\Framework\Exception($th->getMessage());
		}

		return self::setAutoInstance($refClass->newInstanceArgs($argsToInject), $originalClass);
	}
}
