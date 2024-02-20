<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Session\Api;

/**
 * @since 2.0.0
 * @package Phacil\Framework\Session\Api
 * @api
 */
interface HandlerInterface extends \SessionHandlerInterface {

	/**
	 * Get the number of failed lock attempts
	 *
	 * @return int
	 * @throws \Phacil\Framework\Exception
	 */
	public function getFailedLockAttempts();

	/**
	 * @param string $name 
	 * @return $this 
	 */
	public function setName($name);
}