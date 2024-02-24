<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Exception;

/**
 * Exception thrown when an illegal index was requested. This represents errors that should be detected at compile time.
 * 
 * @since 2.0.0
 * @api
 * @package Phacil\Framework\Exception
 */
class OutOfRangeException extends \Phacil\Framework\Exception\LogicException {

}