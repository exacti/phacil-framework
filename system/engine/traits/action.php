<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Traits;

/**
 * Trait for the Action class
 * 
 * Code reused in ActionSystem class
 */
trait Action {

	/**
	 * 
	 * @var string
	 */
	protected $file;
	
	/**
	 * 
	 * @var string
	 */
	protected $class;
	
	/**
	 * 
	 * @var string
	 */
	protected $method;
	
	/**
	 * 
	 * @var array
	 */
	protected $args = array();

	/**
	 * 
	 * @var (string[]|string|null)[]
	 */
	private $classAlt = [];

	/** @return string  */
	public function getFile():string {
		return $this->file;
	}
	
	/** @return string  */
	public function getClass():string {
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
	public function getClassAlt():array {
		return $this->classAlt;
	}
	
	/** @return string  */
	public function getMethod():string {
		return $this->method;
	}
	
	/** @return array  */
	public function getArgs():array {
		return $this->args;
	}
}