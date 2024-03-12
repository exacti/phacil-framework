<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Api;

interface Factory {
	/**
	 * Creates an instance of the class configured in the factory.
	 *
	 * @param array $args (Optional) Additional arguments for the class constructor.
	 * @return \Phacil\Framework\MagiQL The created instance.
	 * 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function create(array $args = []);
}