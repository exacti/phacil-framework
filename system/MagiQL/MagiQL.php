<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Databases\Api\DriverInterface as DatabaseDriverInterface;
use Phacil\Framework\MagiQL\Builder\AbstractBuilder;

class MagiQL extends AbstractBuilder {

	const SELECT_KEY = 'SELECT';
	const FROM_KEY = 'FROM';
	const WHERE_KEY = 'WHERE';
	const ORDER_KEY = 'ORDER';
	const GROUP_KEY = 'GROUP';
	const JOIN_KEY = 'JOIN';

	/**
	 * 
	 * @var \Phacil\Framework\Api\Database
	 */
	private $db;

	/**
	 * 
	 * @var array
	 */
	protected $queryArray;

	public function __construct(\Phacil\Framework\Api\Database $db) {
		$this->db = $db;

		parent::__construct();
	}

	/**
	 * 
	 * @param \Phacil\Framework\MagiQL\Api\QueryInterface $obj 
	 * @return \Phacil\Framework\Databases\Api\Object\ResultInterface|true 
	 * @throws \Phacil\Framework\MagiQL\Builder\BuilderException 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function execute(\Phacil\Framework\MagiQL\Api\QueryInterface $obj) {
		$query = $this->write($obj);
		$values = $this->getValues();
		return $this->db->execute($query, $values);
	}

	/**
	 * 
	 * @return \Phacil\Framework\Api\Database 
	 */
	public function getDb() { 
		return $this->db;
	}

	/**
	 * @param string $tableName 
	 * @return bool 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function isTableExists($tableName) {
		if($this->db->getDBTypeId() === DatabaseDriverInterface::LIST_DB_TYPE_ID['MYSQL']){
			$sql = 'SELECT (1) AS tbl_exists FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :v1 AND TABLE_SCHEMA = :v2';
			$result = $this->db->execute($sql, [
				':v1' => $tableName,
				':v2' => \Phacil\Framework\Config::DB_DATABASE()
			]);
		}
		if($this->db->getDBTypeId() === DatabaseDriverInterface::LIST_DB_TYPE_ID['MSSQL']){
			$sql = "SELECT OBJECT_ID(:v1, 'U') AS table_id";
			$result = $this->db->execute($sql, [
				':v1' => $tableName
			]);
		}
		if($this->db->getDBTypeId() === DatabaseDriverInterface::LIST_DB_TYPE_ID['POSTGRE']){
			$sql = "SELECT 1 FROM information_schema.tables WHERE table_name = :v1";
			$result = $this->db->execute($sql, [
				':v1' => $tableName
			]);
		}
		if($this->db->getDBTypeId() === DatabaseDriverInterface::LIST_DB_TYPE_ID['SQLLITE3']){
			$sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=:v1";
			$result = $this->db->execute($sql, [
				':v1' => $tableName
			]);
		}

		if($result && $result->getNumRows() > 0){
			return true;
		}

		return false;
	}

}