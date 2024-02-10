<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object;

use Phacil\Framework\Databases\Object\ResultInterface;

/**
 * Result Databse iterator class with memory cache.
 * It is important to understand that most classes that do not implement Iterators have reasons as most likely they do not allow the full Iterator feature set. If so, techniques should be provided to prevent misuse, otherwise expect exceptions or fatal errors.
 * @since 2.0.0
 * @package Phacil\Framework\Databases\Object
 */
class ResultCacheIterator extends \CachingIterator implements ResultInterface {

	/**
	 * 
	 * @var int
	 */
	public $num_rows;

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Object\Item[]|\Iterator|null
	 */
	public $data = null;
	

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Object\Result
	 */
	private $Iterator;

	/**
	 * @param array|\Phacil\Framework\Databases\Object\ResultInterface $results 
	 * @param int $flags 
	 * @return $this 
	 */
	public function __construct($results = [], $flags = \CachingIterator::FULL_CACHE)
	{
		if(is_array($results))
			$this->Iterator = new \Phacil\Framework\Databases\Object\Result($results);
		elseif($results instanceof \Phacil\Framework\Databases\Object\ResultInterface)
			$this->Iterator = $results;

		parent::__construct($this->Iterator, $flags);
		//$this->num_rows = $this->count();
		return $this;
	}

	/**
	 * 
	 * @param string $name 
	 * @return \Phacil\Framework\Databases\Object\ItemInterface[]|\Phacil\Framework\Databases\Object\ItemInterface|null 
	 * @throws \Phacil\Framework\Exception\RuntimeException 
	 */
	public function __get($name)
	{

		switch ($name) {
			case 'rows':
				return $this->__toArray();
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
	public function getData($numRow = false)
	{
		return $numRow ? $this->getRow($numRow) : $this->getRows();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getItems()
	{
		return $this->__toObject();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRows($rows)
	{
		$this->rows = $rows;
		$this->row = isset($rows[0]) ? $this->rows[0] : null;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRows()
	{
		return $this->rows;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRow($row)
	{
		$this->row = $row;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRow($numRow = false)
	{
		return ($numRow ? ($this->offsetGet($numRow - 1) ?: null) : $this->row);
	}

	/**
	 * 
	 * {@inheritdoc}
	 */
	public function setNumRows($num)
	{
		$this->num_rows = $num;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNumRows()
	{
		return $this->num_rows;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toObject()
	{
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toArray()
	{
		//return iterator_to_array($this);
		foreach ($this as $val) {
			# nothing
		}
		return $this->getCache();
	}

	/**
	 * {@inheritdoc}
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($index)
	{
		$data = null;
		if ($this->offsetExists($index)) {
			$data = parent::offsetGet($index);
		} elseif ($this->Iterator->offsetExists($index)){
			$data = $this->Iterator->offsetGet($index);
		}

		if(!$data) return null;

		/** @var \Phacil\Framework\Databases\Object\ItemInterface */
		$item = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Object\ItemInterface::class, [$data]);
		
		return $item;
	}

}