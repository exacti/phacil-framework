<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax\Adapt\PostgreSQL;

use Phacil\Framework\MagiQL\Builder\Syntax\UpdateWriter as GenericWriter;
use Phacil\Framework\MagiQL\Manipulation\Update;
use Phacil\Framework\MagiQL\Syntax\Column;

class UpdateWriter extends GenericWriter
{
	/**
	 * @param Update $update
	 *
	 * @return string
	 */
	protected function writeUpdateValues(Update $update)
	{
		$assigns = [];
		foreach ($update->getValues() as $column => $value) {
			$value = $this->writer->writePlaceholderValue($value);

			$assigns[] = "$column = $value";
		}

		return \implode(', ', $assigns);
	}

	/**
	 * Creates a Column object.
	 *
	 * @param array      $argument
	 * @param null|Table $table
	 *
	 * @return Column
	 */
	public static function createColumn(array &$argument, $table = null)
	{
		$columnName = \array_values($argument);
		$columnName = $columnName[0];

		$columnAlias = \array_keys($argument);
		$columnAlias = $columnAlias[0];

		if (\is_numeric($columnAlias) || \strpos($columnName, '*') !== false) {
			$columnAlias = null;
		}

		return new Column($columnName, (string) $table, $columnAlias);
	}
}
