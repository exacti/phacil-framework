<?php 
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Databases\Object;

/**
 * @property int $num_rows
 * @property array $row
 * @property array $rows
 * @package Phacil\Framework\Databases\Object
 */
interface ResultInterface {
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
	 * @return array 
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
	 * @return array 
	 */
	public function getData($numRow = false);
}