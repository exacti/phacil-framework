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
use \stdClass;

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
	 * @param string $hostname 
	 * @param string $username 
	 * @param string $password 
	 * @param string $database 
	 * @param string $port 
	 * @param string $charset 
	 * @return void 
	 * @throws Exception 
	 */
	public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'utf8mb4') {
		try {
			$this->connection = @new \MySQLi($hostname, $username, $password, $database, $port);
		} catch (\mysqli_sql_exception $e) {
			throw new \Exception('Error: ' . $this->connection->error . '<br />Error No: ' . $this->connection->errno);
		}

		if (!$this->connection->connect_errno) {
			$this->connection->report_mode = MYSQLI_REPORT_ERROR;
			$this->connection->set_charset($charset);
			//$this->connection->query("SET SESSION sql_mode = 'NO_ZERO_IN_DATE,NO_ENGINE_SUBSTITUTION'");
			$this->connection->query("SET SQL_MODE = ''");
		} else {
			throw new \Exception('Error: ' . $this->connection->error . '<br />Error No: ' . $this->connection->errno);
		}

	}
	
	/**
	 * Execute the SQl Query
	 * @param string $sql 
	 * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
	 * @throws \Exception 
	 */
	public function query($sql) {
		$query = $this->connection->query($sql);
		if (!$this->connection->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();
				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}
				$result = new \Phacil\Framework\Databases\Object\Result();
				$result->setNumRows($query->num_rows);
				$result->setRow(isset($data[0]) ? $data[0] : []);
				$result->setRows($data);
				$query->close();
				return $result;
			} else {
				return true;
			}
		} else {
			throw new \Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $sql);
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
}
