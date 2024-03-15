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
 * 
 * @since 2.0.0
 * @package Phacil\Framework
 * @api
 */
class Exception extends \Exception implements \Phacil\Framework\Exception\Throwable
{
	public $errorFormat = 'text';

	protected $heritageTrace = false;

	protected $exceptionFile = self::DEFAULT_EXCEPTION_FILE;

	/**
	 * 
	 * @param \Exception $object 
	 * @return $this 
	 */
	public function setObject($object){
		$this->message = get_class($object).": ".$object->getMessage();
		$this->line = $object->getLine();
		$this->heritageTrace = $object->getTrace();
		$this->file = $object->getFile();
		return $this;
	}

	/**
	 * Save the exceptions in the exceptions log file
	 * @return void 
	 */
	public function __destruct()
	{
		$debugging = (\Phacil\Framework\Config::DEBUG()) ?: false;
		$this->errorFormat = \Phacil\Framework\Config::DEBUG_FORMAT() ?: $this->errorFormat;

		$log = new \Phacil\Framework\Log($this->exceptionFile);

		$errorStamp = [
			'message' => $this->getMessage(),
			'line' => $this->getLine(),
			'file' => $this->getFile(),
			'trace' => ($debugging) ? (($this->errorFormat == 'json') ? ($this->heritageTrace ?: $this->getTrace()) : Debug::trace(($this->heritageTrace ?: $this->getTrace()))) : null
		];

		if($this->getCode() > 0)
			$errorStamp['code'] = $this->getCode();

		if($this->getCode() < 200 || $this->getCode() > 499) {
			$log->critical(($this->errorFormat == 'json') ? json_encode($errorStamp) : implode(PHP_EOL, array_map(
				[self::class, 'convertArray'],
				$errorStamp,
				array_keys($errorStamp)
			)));
			return;
		}
		$log->write(($this->errorFormat == 'json') ? json_encode($errorStamp) : implode(PHP_EOL, array_map(
			[self::class,'convertArray'],
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

