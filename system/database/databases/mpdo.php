<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use PDO;
use Phacil\Framework\Interfaces\Databases;

/** 
 * Alternative PDO MySQL connection method.
 * 
 * @package Phacil\Framework\Databases */
final class mPDO implements Databases {
	/**
	 * 
	 * @var PDO
	 */
	private $connection = null;

	/**
	 * 
	 * @var int
	 */
	private $rowCount;

	/**
	 * 
	 * @var mixed
	 */
	private $statement = null;
	
	public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'UTF8') {
		try {
			$dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset={$charset}";
			$options = array(
				\PDO::ATTR_PERSISTENT => true,
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
			
			$this->connection = new \PDO($dsn, $username, $password, $options);
		} catch(\PDOException $e) {
			throw new \Exception('Failed to connect to database. Reason: \'' . $e->getMessage() . '\'');
		}
		$this->connection->exec("SET NAMES 'utf8'");
		$this->connection->exec("SET CHARACTER SET utf8");
		$this->connection->exec("SET CHARACTER_SET_CONNECTION=utf8");
		$this->connection->exec("SET SQL_MODE = ''");
        $this->rowCount = 0;
	}
	public function prepare($sql) {
		$this->statement = $this->connection->prepare($sql);
	}
	public function bindParam($parameter, $variable, $data_type = \PDO::PARAM_STR, $length = 0) {
		if ($length) {
			$this->statement->bindParam($parameter, $variable, $data_type, $length);
		} else {
			$this->statement->bindParam($parameter, $variable, $data_type);
		}
	}
	public function execute() {
		try {
			if ($this->statement && $this->statement->execute()) {
				$data = array();
				while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
					$data[] = $row;
				}
				$result = new \stdClass();
				$result->row = (isset($data[0])) ? $data[0] : array();
				$result->rows = $data;
				$result->num_rows = $this->statement->rowCount();
			}
		} catch(\PDOException $e) {
			throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode());
		}
	}
	public function query($sql, $params = array()) {
		$this->statement = $this->connection->prepare($sql);
		
		$result = false;
		try {
			if ($this->statement && $this->statement->execute($params)) {
				$data = array();
                $this->rowCount = $this->statement->rowCount();
                if($this->rowCount > 0)
                {
                    try {
                        $data = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
                    }
                    catch(\Exception $ex){}
                }
                // free up resources
                $this->statement->closeCursor();
                $this->statement = null;
				$result = new \Phacil\Framework\Databases\Object\Result();
				$result->row = (isset($data[0]) ? $data[0] : array());
				$result->rows = $data;
				$result->num_rows = $this->rowCount;
			}
		} catch (\PDOException $e) {
			throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode() . ' <br />' . $sql);
		}
		if ($result) {
			return $result;
		} else {
			$result = new \Phacil\Framework\Databases\Object\Result();
			$result->row = array();
			$result->rows = array();
			$result->num_rows = 0;
			return $result;
		}
	}
	public function escape($value) {
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}
	public function countAffected() {
		if ($this->statement) {
			return $this->statement->rowCount();
		} else {
			return $this->rowCount;
		}
	}
	public function getLastId() {
		return $this->connection->lastInsertId();
	}
	
	public function isConnected() {
		if ($this->connection) {
			return true;
		} else {
			return false;
		}
	}
	
	public function __destruct() {
		unset($this->connection);
	}
}