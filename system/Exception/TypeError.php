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
class TypeError extends \Phacil\Framework\Exception\Error
{
	/* Properties */
	protected $severity = E_COMPILE_ERROR;
}