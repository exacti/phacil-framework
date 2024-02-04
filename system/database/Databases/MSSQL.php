<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

/** 
 * Legacy class to connect a MS SQL Server with PHP 5 legacy driver.
 * 
 * Doesn't work with PHP 7+
 * @deprecated 2.0.0
 * @see \Phacil\Framework\Databases\sqlsrvPDO
 * @package Phacil\Framework\Databases */
class MSSQL implements \Phacil\Framework\Interfaces\Databases
{

	const DB_TYPE = 'Microsoft SQL Server Database';

	const DB_TYPE_ID = self::LIST_DB_TYPE_ID['MSSQL'];

	private $connection;

	public function __construct($hostname, $username, $password, $database, $port = '1443', $charset = 'utf8')
	{
		if (!$this->connection = \mssql_connect($hostname, $username, $password)) {
			throw new \Phacil\Framework\Exception('Error: Could not make a database connection using ' . $username . '@' . $hostname);
		}

		if (!\mssql_select_db($database, $this->connection)) {
			throw new \Phacil\Framework\Exception('Error: Could not connect to database ' . $database);
		}

		\mssql_query("SET NAMES 'utf8'", $this->connection);
		\mssql_query("SET CHARACTER SET utf8", $this->connection);
		\mssql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
	}

	public function query($sql)
	{
		$resource = \mssql_query($sql, $this->connection);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($result = \mssql_fetch_assoc($resource)) {
					$data[$i] = $result;

					$i++;
				}

				\mssql_free_result($resource);

				/** @var \Phacil\Framework\Databases\Object\ResultInterface */
				$query = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$data]);
				$query->setNumRows($i);

				unset($data);

				return $query;
			} else {
				return true;
			}
		} else {
			throw new \Phacil\Framework\Exception('Error: ' . mssql_get_last_message($this->connection) . '<br />' . $sql);
		}
	}

	public function escape($value)
	{
		return \mssql_real_escape_string($value, $this->connection);
	}

	public function countAffected()
	{
		return \mssql_rows_affected($this->connection);
	}

	public function getLastId()
	{
		$last_id = false;

		$resource = \mssql_query("SELECT @@identity AS id", $this->connection);

		if ($row = mssql_fetch_row($resource)) {
			$last_id = trim($row[0]);
		}

		\mssql_free_result($resource);

		return $last_id;
	}

	function isConnected()
	{
	}

	public function __destruct()
	{
		\mssql_close($this->connection);
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
		$sql = str_replace(array_keys($params), array_values($params), $sql);
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
