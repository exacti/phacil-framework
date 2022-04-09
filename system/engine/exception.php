<?php

/**
 * Copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <oliveira131@hotmail.com>
 */

 
namespace Phacil\Framework;

/**
 * Exception extended for log on destruct
 * @since 2.0.0
 * @package Phacil\Framework
 */
class Exception extends \Exception
{
	public $errorFormat = 'text';

	/**
	 * Save the exceptions in the exceptions log file
	 * @return void 
	 */
	public function __destruct()
	{
		$debugging = (\Phacil\Framework\Config::DEBUG()) ?: false;
		$this->errorFormat = \Phacil\Framework\Config::DEBUG_FORMAT() ?: $this->errorFormat;

		$log = new \Phacil\Framework\Log("exception.log");

		$errorStamp = [
			'error' => $this->getMessage(),
			'line' => $this->getLine(),
			'file' => $this->getFile(),
			'trace' => ($debugging) ? (($this->errorFormat == 'json') ? $this->getTrace() : Debug::trace($this->getTrace())) : null
		];
		$log->write(($this->errorFormat == 'json') ? json_encode($errorStamp) : implode(PHP_EOL, array_map(
			['self','convertArray'],
			$errorStamp,
			array_keys($errorStamp)
		)));
	}

	/**
	 * 
	 * @param string|array $v 
	 * @param string $k 
	 * @return string 
	 */
	static public function convertArray($v, $k) {
		return sprintf("%s: %s", $k, (is_array($v) ? json_encode($v) : $v));
	}
	
}

