<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use MySQLi as GlobalMysqli;
use Phacil\Framework\Interfaces\Databases;

/** 
 * Default driver to connect a MySQL/MariaDB databases.
 * 
 * Works on most of PHP instalations 
 * 
 * @package Phacil\Framework\Databases */
class MySQLi implements Databases {
	/**
	 * 
	 * @var GlobalMysqli
	 */
	private $connection;

	/**
	 * @var string
	 */
	const DB_TYPE = 'MySQL';

	/**
	 * @var int
	 */
	const DB_TYPE_ID = self::LIST_DB_TYPE_ID['MYSQL'];

	/**
	 * @param string $hostname 
	 * @param string $username 
	 * @param string $password 
	 * @param string $database 
	 * @param string $port 
	 * @param string $charset 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'utf8mb4') {
		try {
			$this->connection = @new \MySQLi($hostname, $username, $password, $database, $port);
		} catch (\mysqli_sql_exception $e) {
			throw new \Phacil\Framework\Exception('Error: ' . $this->connection->error . '<br />Error No: ' . $this->connection->errno);
		}

		if (!$this->connection->connect_errno) {
			if(isset($this->connection->report_mode))
				$this->connection->report_mode = MYSQLI_REPORT_ERROR;

			$this->connection->set_charset($charset);
			//$this->connection->query("SET SESSION sql_mode = 'NO_ZERO_IN_DATE,NO_ENGINE_SUBSTITUTION'");
			$this->connection->query("SET SQL_MODE = ''");
		} else {
			throw new \Phacil\Framework\Exception('Error: ' . $this->connection->error . '<br />Error No: ' . $this->connection->errno);
		}

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

	/**
	 * Execute the SQl Query
	 * @param string $sql 
	 * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function query($sql) {
		$query = $this->connection->query($sql);
		if (!$this->connection->errno) {
			if ($query instanceof \mysqli_result) {
				/** @var \Phacil\Framework\Databases\Object\ResultInterface */
				$result = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$query->fetch_all(MYSQLI_ASSOC)]);
				$result->setNumRows($query->num_rows);
				$query->close();
				return $result;
			} else {
				return true;
			}
		} else {
			throw new \Phacil\Framework\Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $sql);
		}
	}
	
	/**
	 * Important escape to prevent SQL injection.
	 * 
	 * @param string $value 
	 * @return string 
	 */
	public function escape($value) {
		return $this->connection->real_escape_string($value);
	}
	
	/** 
	 * Number of affected rows
	 * 
	 * @return int  */
	public function countAffected() {
		return $this->connection->affected_rows;
	}

	/** @return int|string  */
	public function getLastId() {
		return $this->connection->insert_id;
	}
	
	/** @return bool  */
	public function isConnected() {
		return $this->connection->ping();
	}
	
	/** @return void  */
	public function __destruct() {
		$this->connection->close();
	}

	/**
	 * Execute a prepared statement with parameters
	 *
	 * @param string $sql SQL query with named placeholders
	 * @param array $params Associative array of parameters
	 * @return \Phacil\Framework\Databases\Object\ResultInterface|true
	 * @throws \Phacil\Framework\Exception 
	 */
	public function execute($sql, array $params = [])
	{
		// Verificar se há parâmetros e fazer o bind
		if (!empty($params)) {
			$types = '';
			$bindParams = [];

			//$sql = str_replace(array_keys($params), array_fill(0, count($params), '?'), $sql);

			foreach ($params as $placeholder => &$param) {

				//$stmt->bind_param($this->getParamType($param), $param);
				$types .= $this->getParamType($param);
				$bindParams[] = &$param;

				if (is_string($placeholder))
					$sql = str_replace($placeholder, '?', $sql);
			}

			$stmt = $this->connection->prepare($sql);

			array_unshift($bindParams, $types);
			call_user_func_array([$stmt, 'bind_param'], $bindParams);
		} else {
			$stmt = $this->connection->prepare($sql);
		}

		if ($stmt === false) {
			throw new \Phacil\Framework\Exception('Error preparing query: ' . $this->connection->error);
		}

		$result_exec = $stmt->execute();

		if ($stmt->errno) {
			throw new \Phacil\Framework\Exception('Error executing query: ' . $stmt->error);
		}

		$result = $stmt->get_result();

		if ($result === false && !empty($stmt->error_list)) {
			throw new \Phacil\Framework\Exception('Error getting result: ' . $stmt->error);
		}

		// Processar resultados se for um SELECT
		if ($result instanceof \mysqli_result) {
			//$resultObj = new \Phacil\Framework\Databases\Object\Result($result->fetch_all(MYSQLI_ASSOC));
			/** @var \Phacil\Framework\Databases\Object\ResultInterface */
			$resultObj = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$result->fetch_all(MYSQLI_ASSOC)]);
			$resultObj->setNumRows($result->num_rows);

			$result->close();

			return $resultObj;
		}

		// Se não for um SELECT, apenas retornar verdadeiro
		return $result_exec;
	}

	/**
	 * Check type
	 * @param mixed $value 
	 * @return string 
	 */
	private function getParamType($value)
	{
		switch (true) {
			case is_int($value):
				return 'i';
			case is_float($value):
				return 'd';
			case is_string($value):
				return 's';
			default:
				return 'b';
		}
	}
}