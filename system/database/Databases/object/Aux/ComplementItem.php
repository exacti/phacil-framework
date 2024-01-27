<?php 
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object\Aux;

abstract class ComplementItem {
	/**
	 * 
	 * @var array|null
	 */
	protected $__data = null;

	/**
	 * 
	 * @return int<0, \max> 
	 */
	public function count(): int {
		return count($this->__data);
	}

	/**
	 * 
	 * @return \Traversable<mixed, mixed>|mixed[] 
	 */
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->__data);
	}
}