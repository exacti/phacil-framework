<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Interfaces;

/**
 * 
 * @package Phacil\Framework\Interfaces
 */
interface Controller extends \Phacil\Framework\Interfaces\Common\Registers {

	/**
	 * 
	 * {@inheritdoc}
	 */
	static public function getInstance();
}