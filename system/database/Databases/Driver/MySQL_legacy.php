<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Driver;

/** 
 * Legacy class to connect a MySQL with PHP 5 legacy driver.
 * 
 * Doesn't work with PHP 7+
 * @package Phacil\Framework\Databases 
 * @deprecated 2.0.0 Use MySQLi class driver instead.
 * @see \Phacil\Framework\Databases\MySQLi
 * */
class MySQL_legacy implements \Phacil\Framework\Databases\Api\DriverInterface {

	const DB_TYPE = 'MySQL';

	const DB_TYPE_ID = 1;

	private $connection;
	
	/** {@inheritdoc} */
	public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'utf8') {
		if (!$this->connection = \mysql_connect($hostname, $username, $password)) {
      		throw new \Phacil\Framework\Exception('Error: Could not make a database connection using ' . $username . '@' . $hostname);
    	}

    	if (!\mysql_select_db($database, $this->connection)) {
      		throw new \Phacil\Framework\Exception('Error: Could not connect to database ' . $database);
    	}
		
		\mysql_query("SET NAMES '".$charset."'", $this->connection);
		\mysql_query("SET CHARACTER SET ".$charset."", $this->connection);
		\mysql_query("SET CHARACTER_SET_CONNECTION=".$charset."", $this->connection);
		\mysql_query("SET SQL_MODE = ''", $this->connection);
  	}

	public function isConnected() { }
		
	/**
	 * {@inheritdoc}
	 */
  	public function query($sql) {
		$resource = \mysql_query($sql, $this->connection);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;
    	
				$data = array();
		
				while ($result = \mysql_fetch_assoc($resource)) {
					$data[$i] = $result;
    	
					$i++;
				}
				
				\mysql_free_result($resource);

				/**
				 * @var \Phacil\Framework\Databases\Api\Object\ResultInterface
				 */
				$query = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Api\Object\ResultInterface", [$data]);
				$query->setNumRows($i);
				
				unset($data);

				return $query;	
    		} else {
				return true;
			}
		} else {
			throw new \Phacil\Framework\Exception('Error: ' . \mysql_error($this->connection) . '<br />Error No: ' . mysql_errno($this->connection) . '<br />' . $sql);
    	}
  	}

	/** {@inheritdoc} */
	public function escape($value) {
		return \mysql_real_escape_string($value, $this->connection);
	}

	/** {@inheritdoc} */
  	public function countAffected() {
    	return \mysql_affected_rows($this->connection);
  	}

	/** {@inheritdoc} */
  	public function getLastId() {
    	return \mysql_insert_id($this->connection);
  	}

	/**
	 * {@inheritdoc}
	 */
	public function execute($sql, array $params = [])
	{
		foreach ($params as $placeholder => &$param) {
			$bindParams[] = $this->escape($param);

			if (is_string($placeholder))
				$sql = str_replace($placeholder, $this->escape($param), $sql);
		}
		$sql = str_replace(array_keys($params), array_values($bindParams), $sql);
		return $this->query($sql);
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
