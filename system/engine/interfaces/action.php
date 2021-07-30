<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Interfaces;

interface Action {

	/**
	 * @param string $route 
	 * @param array $args 
	 * @return void 
	 */
	public function __construct($route, $args = array());

	/** @return string  */
	public function getFile():string;

	/** @return string  */
	public function getClass():string;

	/**
	 * 
	 * @param string $class 
	 * @return $this 
	 */
	public function setClass($class);

	/** @return array  */
	public function getClassAlt():array;

	/** @return string  */
	public function getMethod():string;

	/** @return array  */
	public function getArgs():array;
}