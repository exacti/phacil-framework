<?php 
/*
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */

namespace Phacil\Framework\Databases\Object\Aux;

abstract class ComplementItemLegacy {
	/**
	 * 
	 * @var array|null
	 */
	protected $__data = null;

	/**
	 * 
	 * @return int<0, \max> 
	 */
	public function count() {
		return count($this->__data);
	}

	/**
	 * 
	 * @return \Traversable<mixed, mixed>|mixed[] 
	 */
	public function getIterator() {
		return new \ArrayIterator($this->__data);
	}
}