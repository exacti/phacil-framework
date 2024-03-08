<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\MagiQL\Builder\Syntax\Adapt\SQLite3;

use Phacil\Framework\MagiQL\Builder\Syntax\ColumnWriter as GenericWriter;
use Phacil\Framework\MagiQL\Syntax\Column;

class ColumnWriter extends GenericWriter {
	/**
	 * @param Column $column
	 *
	 * @return string
	 */
	public function writeColumnWithoutTable(Column $column)
	{
		$columnString = $this->writer->writeColumnName($column);

		return $columnString;
	}
}
