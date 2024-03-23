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
class ErrorException extends \Phacil\Framework\Exception\Error
{
	/* Properties */
	protected $severity = E_ERROR;

	/**
	 * 
	 * @var \ErrorException
	 */
	protected $errorException;

	/**
	 * @param string $message 
	 * @param int $code 
	 * @param int $severity 
	 * @param null|string $filename 
	 * @param null|int $line 
	 * @param null|\Phacil\Framework\Exception\Throwable $previous 
	 * @return void 
	 */
	public function __construct(
		$message = "",
		$code = 0,
		$severity = E_ERROR,
		$filename = null,
		$line = null,
		\Phacil\Framework\Exception\Throwable $previous = null
	) {
		//$this->errorException = new \ErrorException($message, $code, $severity, $filename, $line, $previous);
		parent::__construct($message, $code, $previous);
		//$this->setObject($this->errorException);
		if($filename)
			$this->file = $filename;

		if($line)
			$this->line = $line;

		$this->severity = $severity;
	}

	/** @return int  */
	final public function getSeverity() {
		return $this->severity;
	}
}