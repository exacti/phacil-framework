<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Api\Object;

/**
 * @method mixed getValue(string $field) 
 * @package Phacil\Framework\Databases\Object
 */
interface ItemInterface extends \Countable, \IteratorAggregate, \Serializable {
	/**
	 * 
	 * @param array $data 
	 * @return $this 
	 */
	public function setData(array $data);

	/**
	 * 
	 * @param string $key 
	 * @param mixed $value 
	 * @return $this 
	 */
	public function setValue($key, $value);

	/**
	 * 
	 * @param string $method 
	 * @param string[] $args 
	 * @return mixed 
	 */
	public function __call($method, $args);
}