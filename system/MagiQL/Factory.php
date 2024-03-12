<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL;

use Phacil\Framework\Factory as FrameworkFactory;

class Factory extends FrameworkFactory implements \Phacil\Framework\MagiQL\Api\Factory {

	public function __construct()
	{
		$this->class = \Phacil\Framework\MagiQL::class;
	}
}