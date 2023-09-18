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

if (version_compare(phpversion(), "7.1.0", ">=")) {
	class ComplementItem extends \Phacil\Framework\Databases\Object\Aux\ComplementItem
	{
	}
} else {
	class ComplementItem extends \Phacil\Framework\Databases\Object\Aux\ComplementItemLegacy
	{
	}
}

/**
 * {@inheritdoc}
 * @method mixed getValue(string $field) 
 * @method mixed getValues(string field, ...) 
 * @package Phacil\Framework\Databases\Object
 */
class Item extends ComplementItem implements ObjectInterface {

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
	 * @param string $key 
	 * @return mixed 
	 */
	private function _getValue($key){
		return isset($this->__data[$key]) ? $this->__data[$key] : null;
	}

	public function __get($key){
		return isset($this->__data[$key]) ? $this->__data[$key] : null;
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