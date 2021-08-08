<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** 
 * Generic class to use stdClass objects compatibility
 * 
 * 
 * Works with all PHP versions that this framework is executable.
 * 
 * @package Phacil\Framework 
 * */
class stdClass extends \stdClass {

	/**
	 * @param string $name 
	 * @return mixed 
	 */
	public function __get($name)
	{
		return $this->$name;
	}

	/**
	 * @param string $name 
	 * @param mixed $value 
	 * @return void 
	 */
	public function __set($name, $value)
	{
		$this->$name = $value;
	}
}