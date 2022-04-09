<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Config {
	/**
	 * 
	 * @var array
	 */
	private $data = array();

  	/**
  	 * @param string|int $key 
  	 * @return null|array|string 
  	 */
  	public function get($key) {
    	return (isset($this->data[$key]) ? $this->data[$key] : null);
  	}	
	
	/**
	 * @param string $key 
	 * @param mixed $value 
	 * @return void 
	 */
	public function set($key, $value) {
    	$this->data[$key] = $value;
  	}

	/**
	 * @param string|int $key 
	 * @return bool 
	 */
	public function has($key) {
    	return isset($this->data[$key]);
  	}

	/**
	 * 
	 * @param string $key 
	 * @param mixed $value 
	 * @return mixed 
	 */
	static public function __callStatic ($key, $value){
		try {
			return (defined($key)) ? constant($key) : self::setConstant($key, $value);
		} catch (\Phacil\Framework\Exception $th) {
			throw new \Phacil\Framework\Exception($th->getMessage());
		}
	}

	/**
	 * 
	 * @param string $key 
	 * @param mixed $value 
	 * @return mixed 
	 */
	static private function setConstant($key, $value){
		if(is_array($value) && count($value) == 1){
			$value = end($value);
		}
		$php = version_compare(phpversion(), '7.0', '>=');
		if(is_array($value) && !$php){
			throw new \Phacil\Framework\Exception('Array is not supported on constant value in PHP <7');
		}
		return (define($key, $value)) ? $value : null;
	}

  	/**
  	 * @param string $filename 
  	 * @return void 
  	 */
  	public function load($filename) {
		$file = self::DIR_CONFIG() . $filename . '.php';
		
    	if (file_exists($file)) { 
	  		$cfg = array();
	  
	  		require($file);
	  
	  		$this->data = array_merge($this->data, $cfg);
		} else {
			trigger_error('Error: Could not load config ' . $filename . '!');
			exit();
		}
  	}
}
