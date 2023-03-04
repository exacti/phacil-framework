<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Object;

use Phacil\Framework\Databases\Object\ResultInterface;

class Result implements ResultInterface{

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
	 * {@inheritdoc}
	 */
	public function getData($numRow = false) { 
		return $numRow ? $this->getRow($numRow) : $this->getRows();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRows($rows){
		$this->rows = $rows;
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
		return $numRow ? ($this->rows[$numRow + 1]?? null ) : $this->row;
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
	
}