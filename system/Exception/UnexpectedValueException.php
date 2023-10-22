<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Exception;

/**
 * Exception thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors.
 * 
 * @package Phacil\Framework\Exception
 */
class UnexpectedValueException extends \Phacil\Framework\Exception\RuntimeException {

}