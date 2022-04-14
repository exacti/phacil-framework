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
	 * @param string $key 
	 * @return mixed 
	 */
	public function get($key) {

		return (isset($this->$key) ? $this->$key : $this->engine->checkRegistry($key));
	}

	/**
	 * @param string $key 
	 * @param string $value 
	 * @return void 
	 */
	public function set($key, $value) {
		$this->$key = $value;
	}

	/**
	 * @param string $key 
	 * @return bool 
	 */
	public function has($key) {
    	return isset($this->$key);
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
		if (isset($this->$key)) {
			unset($this->$key);
		}
	}

	/** 
	 * Try to obtain an iniciated engine instance
	 * 
	 * @return \Phacil\Framework\Registry
	 * @since 2.0.0
	 */
	static public function getInstance() {
		return \Phacil\Framework\startEngineExacTI::getRegistry();
	}
}
