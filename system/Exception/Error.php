<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Exception;

/**
 * Exception that represents error in the program logic. This kind of exception should lead directly to a fix in your code.
 * 
 * @since 2.0.0
 * @api
 * @package Phacil\Framework\Exception
 */
class Error extends \Phacil\Framework\Exception
{
	/* Properties */
	protected $severity = E_ERROR;

	/**
	 * 
	 * @var \ErrorException
	 */
	protected $errorException;

	/** @return int  */
	public function getSeverity() {
		return $this->severity;
	}

	/** @return int  */
	public function setSeverity($severity = E_ERROR) {
		return $this->severity = $severity;
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
			'severity' => $this->getSeverity(),
			'file' => $this->getFile(),
			'trace' => ($debugging) ? (($this->errorFormat == 'json') ? ($this->heritageTrace ?: $this->getTrace()) : \Phacil\Framework\Debug::trace(($this->heritageTrace ?: $this->getTrace()))) : null
		];

		if ($this->getCode() > 0)
			$errorStamp['code'] = $this->getCode();

		$log->error(($this->errorFormat == 'json') ? json_encode($errorStamp) : implode(PHP_EOL, array_map(
			[self::class, 'convertArray'],
			$errorStamp,
			array_keys($errorStamp)
		)));
	}
}