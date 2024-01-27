<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object;

use Phacil\Framework\Databases\Object\ResultInterface;
use SplObjectStorage;

if (version_compare(phpversion(), "7.1.0", ">=")) {
	class ComplementResult extends \Phacil\Framework\Databases\Object\Aux\ComplementResult
	{
		
	}

} else {
	class ComplementResult extends \Phacil\Framework\Databases\Object\Aux\ComplementResultLegacy{
	
	}
}

class Result extends ComplementResult implements ResultInterface {

	/**
	 * 
	 * @var array
	 */
	public $rows;

	/**
	 * 
	 * @var array
	 */
	public $row;

	/**
	 * 
	 * @var int
	 */
	public $num_rows;

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Object\Item[]|\SplObjectStorage|\Iterator|null
	 */
	public $data = null;

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
		return $numRow ? (isset($this->rows[$numRow + 1])?$this->rows[$numRow + 1] : null ) : $this->row;
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
		return $this->data ? $this->data : $this->loop($this->rows);
	}

	/**
	 * 
	 * @param mixed $array 
	 * @return \Phacil\Framework\Databases\Object\ItemInterface[] 
	 */
	protected function loop($array)
	{
		if($this->data) return $this->data;

		$this->data = new SplObjectStorage();
		foreach ($array as $key => $value) {
			//$this->data[] = new \Phacil\Framework\Databases\Object\Item($value);
			$obj = new \Phacil\Framework\Databases\Object\Item();
			$this->data->attach($obj->setData($value));
		}

		return $this->data;
	}
	
}