<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object;

use Phacil\Framework\Databases\Object\ResultInterface;
use Phacil\Framework\Databases\Object\ResultCacheIterator;

/**
 * @since 2.0.0
 * @package Phacil\Framework\Databases\Object
 */
class Result extends \ArrayIterator implements ResultInterface {

	/**
	 * 
	 * @var int
	 */
	public $num_rows;

	/**
	 * 
	 * @var ResultCacheIterator
	 */
	private $cachedIterator;

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Object\Item[]|\Iterator|null
	 */
	public $data = null;

	public function __construct(array $results = []) {
		parent::__construct($results);
		//$this->num_rows = $this->count();
		return $this;
	}

	/**
	 * 
	 * @param string $name 
	 * @return \Phacil\Framework\Databases\Object\ItemInterface[]|\Phacil\Framework\Databases\Object\ItemInterface|null 
	 * @throws \Phacil\Framework\Exception\RuntimeException 
	 */
	public function __get($name) {

        switch ($name) {
			case 'rows':
				return $this;
				break;
			
			case 'row':
				return $this->offsetGet(0);
				break;
			
			default:
				throw new \Phacil\Framework\Exception\RuntimeException("Undefined property: $name");
				break;
		}
    }

	/**
	 * 
	 * {@inheritdoc}
	 */
	public function getData($numRow = false) { 
		return $numRow ? $this->getRow($numRow) : $this->getRows();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getItems() {
		return $this->__toObject();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRows($rows){
		$this->rows = $rows;
		$this->row = isset($rows[0]) ? $this->rows[0] : null;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRows(){
		return $this->rows;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRow($row){
		$this->row = $row;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRow($numRow = false){
		return ($numRow ? ($this->offsetGet($numRow - 1) ? : null) : $this->row);
	}

	/**
	 * 
	 * {@inheritdoc}
	 */
	public function setNumRows($num){
		$this->num_rows = $num;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNumRows(){
		return $this->num_rows;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toObject() {
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toArray() {
		return iterator_to_array($this);
		$this->cachedIterator = new ResultCacheIterator($this);
		foreach ($this->cachedIterator as $val) {
			# nothing
		}
		return $this->cachedIterator->getCache();
	}

	/**
	 * 
	 * @param mixed $array 
	 * @return \Phacil\Framework\Databases\Object\ItemInterface[] 
	 */
	protected function loop($array)
	{
		if($this->data) return $this->data;

		$this->data = [];
		foreach ($array as $key => $value) {
			$this->data[] = $value;
		}

		return $this->data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($index)
	{
		if(!$this->offsetExists($index)) return null;

		$data = parent::offsetGet($index);

		$item = new \Phacil\Framework\Databases\Object\Item($data);
		return $item;
	}

	/**
	 * {@inheritdoc}
	 */
	public function current()
	{
		$item = new \Phacil\Framework\Databases\Object\Item(parent::current());
		//$item->setData(parent::current());
		return $item;
	}
	
}