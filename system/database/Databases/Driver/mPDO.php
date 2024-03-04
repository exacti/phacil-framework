<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use PDO;
use Phacil\Framework\Databases\Api\DriverInterface;

/** 
 * Alternative PDO MySQL connection method.
 * 
 * @package Phacil\Framework\Databases */
class mPDO implements DriverInterface {

	const DB_TYPE = 'MySQL';

	const DB_TYPE_ID = 1;

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
	 * @var \PDOStatement
	 */
	private $statement = null;
	
	/** @inheritdoc */
	public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'UTF8') {
		try {
			$dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset={$charset}";
			$options = array(
				\PDO::ATTR_PERSISTENT => true,
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
			
			$this->connection = new \PDO($dsn, $username, $password, $options);
		} catch(\PDOException $e) {
			throw new \Phacil\Framework\Exception('Failed to connect to database. Reason: \'' . $e->getMessage() . '\'');
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

	/** {@inheritdoc} */
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
				/** @var \Phacil\Framework\Databases\Object\ResultInterface */
				$result = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Object\ResultInterface::class, [$data]);
				$result->setNumRows($this->rowCount);
				
			}
		} catch (\PDOException $e) {
			throw new \Phacil\Framework\Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode() . ' <br />' . $sql);
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

	/** {@inheritdoc} */
	public function escape($value) {
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

	/** {@inheritdoc} */
	public function countAffected() {
		if ($this->statement) {
			return $this->statement->rowCount();
		} else {
			return $this->rowCount;
		}
	}

	/** {@inheritdoc} */
	public function getLastId() {
		return $this->connection->lastInsertId();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function isConnected() {
		if ($this->connection) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute($sql, array $params = [])
	{
		try {
			$stmt = $this->connection->prepare($sql);

			if ($stmt === false) {
				throw new \Phacil\Framework\Exception('Error preparing query: ' . implode(', ', $this->connection->errorInfo()));
			}

			// Bind parameters if there are any
			if (!empty($params)) {
				foreach ($params as $placeholder => &$param) {
					$stmt->bindParam($placeholder,$param, $this->getParamType($param));
				}
			}

			$stmt->execute();

			if ($stmt->columnCount()) {
				/** @var \Phacil\Framework\Databases\Object\ResultInterface */
				$data = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Object\ResultInterface::class, [$stmt->fetchAll(\PDO::FETCH_ASSOC)]);
				$data->setNumRows($stmt->rowCount());
				
				$stmt->closeCursor();

				return $data;
			} else {
				$this->rowCount = $stmt->rowCount();
				$stmt->closeCursor();

				return true;
			}
		} catch (\PDOException $exception) {
			throw new \Phacil\Framework\Exception($exception->getMessage());
		}
	}

	/**
	 * 
	 * @param mixed $param 
	 * @return int 
	 */
	private function getParamType(&$param) {
		$paramType = gettype($param);

		switch ($paramType) {
			case 'boolean':
				$paramType = \PDO::PARAM_BOOL;
				break;
			case 'integer':
				$paramType = \PDO::PARAM_INT;
				break;
			case 'double':
			case 'float':
				$paramType = \PDO::PARAM_STR;
				break;
			case 'NULL':
				$paramType = \PDO::PARAM_NULL;
				break;
			default:
				$paramType = \PDO::PARAM_STR;
				break;
		}

		return $paramType;
	}

	/**
	 * 
	 * {@inheritdoc}
	 */
	public function getDBType() { 
		return self::DB_TYPE;
	}

	/**
	 * 
	 * {@inheritdoc}
	 */
	public function getDBTypeId() {
		return self::DB_TYPE_ID;
	 }
}