<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax\Adapt\MSSQL;

use Phacil\Framework\MagiQL\Builder\Syntax\SelectWriter as GenericSelectWriter;
use Phacil\Framework\MagiQL\Manipulation\Select;

class SelectWriter extends GenericSelectWriter
{
	/**
	 * @param Select $select
	 * @param array  $parts
	 *
	 * @return $this
	 */
	protected function writeSelectLimit(Select $select, array &$parts)
	{
		$mask = $this->getStartingLimit($select) . $this->getLimitCount($select);

		$limit = '';

		if ($mask !== '00') {
			$start = $this->placeholderWriter->add($select->getLimitStart());
			$count = $this->placeholderWriter->add($select->getLimitCount());


			//$limit = "LIMIT {$start}, {$count}";
			$limit = "OFFSET {$start} ROWS FETCH NEXT {$count} ROWS ONLY";

			if (\count($select->getAllOrderBy()) < 1) {
				$limit = "ORDER BY 1 ASC " . $limit;
			}
		}

		$parts = \array_merge($parts, [$limit]);

		return $this;
	}
}
