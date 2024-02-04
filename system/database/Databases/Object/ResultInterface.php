<?php 
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Databases\Object;

/**
 * A Database result object with all stored data
 * @since 2.0.0
 * @property int $num_rows
 * @property \Phacil\Framework\Databases\Object\Item $row
 * @property \Phacil\Framework\Databases\Object\Item[] $rows
 * @package Phacil\Framework\Databases\Object
 */
interface ResultInterface extends \Countable, \ArrayAccess, \Traversable  {
	/**
	 * 
	 * @param array $rows 
	 * @return $this 
	 */
	public function setRows($rows);

	/**
	 * 
	 * @return array 
	 */
	public function getRows();

	/**
	 * 
	 * @param array $row 
	 * @return $this 
	 */
	public function setRow($row);

	/**
	 * 
	 * @param int $numRow 
	 * @return \Phacil\Framework\Databases\Object\Item 
	 */
	public function getRow($numRow = false);

	/**
	 * 
	 * @param int $num 
	 * @return $this 
	 */
	public function setNumRows($num);

	/**
	 * 
	 * @return int 
	 */
	public function getNumRows();

	/**
	 * 
	 * @param int $numRow 
	 * @return \Phacil\Framework\Databases\Object\Item[]|\Phacil\Framework\Databases\Object\Item
	 */
	public function getData($numRow = false);

	/**
	 * 
	 * @return \Phacil\Framework\Databases\Object\ResultInterface 
	 */
	public function __toObject();

	/**
	 * 
	 * @return \Phacil\Framework\Databases\Object\Item[] 
	 */
	public function __toArray();

	/**
	 * 
	 * @return \Phacil\Framework\Databases\Object\Item[] 
	 */
	public function getItems();
}