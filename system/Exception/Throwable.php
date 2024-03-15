<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Exception;

if (interface_exists('Throwable')) {
	interface ThrowableBase extends \Throwable {}
} else {
	interface ThrowableBase{
		public function getMessage();
		public function getCode();
		public function getFile();
		public function getLine();
		public function getTrace();
		public function getTraceAsString();
		public function getPrevious();
		public function __toString();
	}
}

/**
 * Throwable is the base interface for any object that can be thrown via a throw statement, including Error and Exception.
 * 
 * Note: PHP classes cannot implement the \Throwable interface directly, and must instead extend \Exception. You can implement this interface on any class.
 * 
 * @since 2.0.0
 * @api
 * @package Phacil\Framework\Exception
 */
interface Throwable extends ThrowableBase {

	const DEFAULT_EXCEPTION_FILE = 'exception.log';

	const DEFAULT_WEBEXCEPTION_FILE = 'web_exception.log';
}

