<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax\Adapt\SQLite3;

use Phacil\Framework\MagiQL\Builder\Syntax\UpdateWriter as GenericWriter;
use Phacil\Framework\MagiQL\Manipulation\Update;
use Phacil\Framework\MagiQL\Syntax\SyntaxFactory;

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
			$newColumn = array($column);
			$column = $this->columnWriter->writeColumn(SyntaxFactory::createColumn($newColumn, null));

			$value = $this->writer->writePlaceholderValue($value);

			$assigns[] = "$column = $value";
		}

		return \implode(', ', $assigns);
	}
}
