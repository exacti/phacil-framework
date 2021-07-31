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
	public function getFile();

	/** @return string  */
	public function getClass();

	/**
	 * 
	 * @param string $class 
	 * @return $this 
	 */
	public function setClass($class);

	/** @return array  */
	public function getClassAlt();

	/** @return string  */
	public function getMethod();

	/** @return array  */
	public function getArgs();
}