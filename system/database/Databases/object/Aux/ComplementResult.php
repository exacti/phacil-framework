<?php
/*
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */

namespace Phacil\Framework\Databases\Object\Aux;

abstract class ComplementResult
{
	/**
	 * {@inheritdoc}
	 * @return int<0, \max> 
	 */
	public function count(): int
	{
		return (int) $this->getNumRows();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator(): \Traversable
	{
		$this->loop($this->rows);
		return $this->data;
		//return new \ArrayIterator($this->loop($this->rows));
	}
}