<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax\Adapt\SQLite3;

use Phacil\Framework\MagiQL\Builder\Syntax\InsertWriter as GenericWriter;
use Phacil\Framework\MagiQL\Api\BuilderInterface;
use Phacil\Framework\MagiQL\Builder\Syntax\PlaceholderWriter;
//use Phacil\Framework\MagiQL\Builder\Syntax\Adapt\SQLite3\ColumnWriter;

class InsertWriter extends GenericWriter
{
	/**
	 * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $writer
	 * @param PlaceholderWriter $placeholder
	 */
	public function __construct(BuilderInterface $writer, PlaceholderWriter $placeholder)
	{
		$this->writer = $writer;
		//$this->columnWriter = new ColumnWriter($this->writer, $placeholder);
	}

	/**
	 * @param $columns
	 *
	 * @return string
	 */
	protected function writeQueryColumns($columns)
	{
		return $this->writeCommaSeparatedValues($columns, $this->writer, 'writeColumnName');
	}
}
