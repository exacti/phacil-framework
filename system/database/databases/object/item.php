<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object;

use Phacil\Framework\Databases\Object\ItemInterface as ObjectInterface;
use Traversable;

/**
 * {@inheritdoc}
 * @method mixed getValue(string $field) 
 * @method mixed getValues(string field, ...) 
 * @package Phacil\Framework\Databases\Object
 */
class Item implements ObjectInterface {
	/**
	 * 
	 * @var array|null
	 */
	protected $__data = null;

	/**
	 * 
	 * @param mixed $data 
	 * @return $this 
	 */
	function __construct($data = null) {
		$this->__data = $data;
		return $this;
	}

	/**
	 * 
	 * @return int<0, \max> 
	 */
	public function count(): int {
		return count($this->__data);
	}

	/**
	 * 
	 * @param array $data 
	 * @return $this 
	 */
	public function setData(array $data) {
		$this->__data = $data;
		//$this->__data = new SplObjectStorage();

		/* foreach($data as $key => $val) {
			$keyForA = new self([$key => $val]);
			$this->__data[$keyForA] = [$key => $val];
		} */
		
		//$this->__data->attach(new self($data));
		//$this->__data->attach(json_decode(json_encode($data)));
		return $this;
	}

	/**
	 * 
	 * @return \Traversable<mixed, mixed>|mixed[] 
	 */
	public function getIterator(): Traversable {
		return new \ArrayIterator($this->__data);
	}

	/**
	 * @param string $key 
	 * @return mixed 
	 */
	private function _getValue($key){
		return $this->__data[$key] ?? null;
	}

	public function __get($key){
		return $this->__data[$key] ?? null;
	}

	/**
	 * 
	 * @param string $key 
	 * @param mixed $value 
	 * @return $this 
	 */
	public function setValue($key, $value){
		$this->__data[$key] = $value;

		return $this;
	}

	/**
	 * 
	 * @param string $method 
	 * @param string[] $args 
	 * @return mixed 
	 */
	public function __call($method, $args) {
		if(($p = strpos($method, 'get')) !== false && $p === 0){
			$fieldname = (str_replace('get', '', $method)); 

			if(!empty($args)){
				switch ($fieldname) {
					case 'Values':
					case 'Value':
						if(count($args) === 1)
							return $this->_getValue($args[0]);

						$values = [];
						foreach($args as $arg){
							$values[$arg] = $this->_getValue($arg);
						}
						return $values;
						break;
					
					default:
						# code...
						break;
				}
				
			}

			//Convert data to insensitive case
			$lower_array_object = new \Phacil\Framework\ArrayClass\CaseInsensitiveArray(
				$this->__data
			);

			return $lower_array_object[$fieldname];
		}
	}
}