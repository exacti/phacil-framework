<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Exception;

/**
 * Exception thrown to indicate range errors during program execution. Normally this means there was an arithmetic error other than under/overflow. This is the runtime version of \Phacil\Framework\Exception\DomainException.
 * 
 * @package Phacil\Framework\Exception
 */
class RangeException extends \Phacil\Framework\Exception\RuntimeException {

}