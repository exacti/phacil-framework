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
 * @package Phacil\Framework\Databases */
final class MSSQL implements \Phacil\Framework\Interfaces\Databases
{
	private $connection;

	public function __construct($hostname, $username, $password, $database, $port = '1443', $charset = 'utf8')
	{
		if (!$this->connection = mssql_connect($hostname, $username, $password)) {
			exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
		}

		if (!mssql_select_db($database, $this->connection)) {
			exit('Error: Could not connect to database ' . $database);
		}

		mssql_query("SET NAMES 'utf8'", $this->connection);
		mssql_query("SET CHARACTER SET utf8", $this->connection);
		mssql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
	}

	public function query($sql)
	{
		$resource = mssql_query($sql, $this->connection);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($result = mssql_fetch_assoc($resource)) {
					$data[$i] = $result;

					$i++;
				}

				mssql_free_result($resource);

				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;

				unset($data);

				return $query;
			} else {
				return true;
			}
		} else {
			trigger_error('Error: ' . mssql_get_last_message($this->connection) . '<br />' . $sql);
			exit();
		}
	}

	public function escape($value)
	{
		return mssql_real_escape_string($value, $this->connection);
	}

	public function countAffected()
	{
		return mssql_rows_affected($this->connection);
	}

	public function getLastId()
	{
		$last_id = false;

		$resource = mssql_query("SELECT @@identity AS id", $this->connection);

		if ($row = mssql_fetch_row($resource)) {
			$last_id = trim($row[0]);
		}

		mssql_free_result($resource);

		return $last_id;
	}

	function isConnected()
	{
	}

	public function __destruct()
	{
		mssql_close($this->connection);
	}
}