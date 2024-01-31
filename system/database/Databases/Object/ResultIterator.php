<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object;

class ResultIterator extends \ArrayIterator {

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($index)
	{
		$data = parent::offsetGet($index);
		$item = new \Phacil\Framework\Databases\Object\Item();
		$item->setData($data);
		return $item;
	}

	public function current()
	{
		$item = new \Phacil\Framework\Databases\Object\Item();
		$item->setData(parent::current());
		return $item;
	}
}