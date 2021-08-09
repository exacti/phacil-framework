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
 * 
 * @see \Phacil\Framework\Interfaces\Action
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

	/** @inheritdoc */
	public function getFile() {
		return $this->file;
	}
	
	/** 
	 * @deprecated 2.0.0 This method return only legacy class. user getClassAlt instead.
	 * @inheritdoc */
	public function getClass() {
		return $this->class;
	}

	private function mountClass($namespace, $class) {
		return (defined('NAMESPACE_PREFIX') ? NAMESPACE_PREFIX."\\" : "").str_replace("/", "\\", (string) $namespace)."Controller\\".(string) $class;
	}

	/**
	 * @inheritdoc
	 */
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}

	/** @inheritdoc  */
	public function getClassAlt() {
		return $this->classAlt;
	}
	
	/** @inheritdoc  */
	public function getMethod() {
		return $this->method;
	}
	
	/** @inheritdoc  */
	public function getArgs() {
		return $this->args;
	}
}