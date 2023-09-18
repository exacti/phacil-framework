<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object\Aux;

abstract class ComplementResultLegacy
{
	/**
	 * {@inheritdoc}
	 * @return int<0, \max> 
	 */
	public function count()
	{
		return (int) $this->getNumRows();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		$this->loop($this->rows);
		return $this->data;
		//return new \ArrayIterator($this->loop($this->rows));
	}
}