<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Exception;

/**
 * Exception for Rest API
 * @package Phacil\Framework\Exception
 */
class WebApiException extends \Phacil\Framework\Exception {

	/**
	 * Error HTTP response codes.
	 */
	const HTTP_BAD_REQUEST = 400;

	const HTTP_UNAUTHORIZED = 401;

	const HTTP_FORBIDDEN = 403;

	const HTTP_NOT_FOUND = 404;

	const HTTP_METHOD_NOT_ALLOWED = 405;

	const HTTP_NOT_ACCEPTABLE = 406;

	const HTTP_INTERNAL_ERROR = 500;

	/**
	 * Fault codes that are used in SOAP faults.
	 */
	const FAULT_CODE_SENDER = 'Sender';
	const FAULT_CODE_RECEIVER = 'Receiver';

	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * 
	 * @param string|array $message 
	 * @param int $code 
	 * @param \Phacil\Framework\Exception\Throwable|null $previous 
	 * @return void 
	 */
	public function __construct($message = "", int $code = 400, $previous = null) {
		parent::__construct((is_array($message) ? \Phacil\Framework\Json::encode($message) : $message), $code, $previous);
	}

}