<?php

/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax\Adapt\Oracle;

use Phacil\Framework\MagiQL\Builder\MySqlBuilder as GenericBuilder;
use Phacil\Framework\MagiQL\Syntax\Column;
use Phacil\Framework\MagiQL\Syntax\Table;

class ColumnTable extends GenericBuilder
{
	/**
	 * @param        $string
	 * @param string $char
	 *
	 * @return string
	 */
	protected function wrapper($string, $char = '"')
	{
		if (0 === strlen($string)) {
			return '';
		}

		return $char . $string . $char;
	}
}
