<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

/** 
 * Legacy class to connect a MySQL with PHP 5 legacy driver.
 * 
 * Doesn't work with PHP 7+
 * @package Phacil\Framework\Databases 
 * */
final class MySQL_legacy implements \Phacil\Framework\Interfaces\Databases {
	private $connection;
	
	public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'utf8') {
		if (!$this->connection = mysql_connect($hostname, $username, $password)) {
      		exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
    	}

    	if (!mysql_select_db($database, $this->connection)) {
      		exit('Error: Could not connect to database ' . $database);
    	}
		
		mysql_query("SET NAMES '".$charset."'", $this->connection);
		mysql_query("SET CHARACTER SET ".$charset."", $this->connection);
		mysql_query("SET CHARACTER_SET_CONNECTION=".$charset."", $this->connection);
		mysql_query("SET SQL_MODE = ''", $this->connection);
  	}

	public function isConnected() { }
		
  	public function query($sql) {
		$resource = mysql_query($sql, $this->connection);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;
    	
				$data = array();
		
				while ($result = mysql_fetch_assoc($resource)) {
					$data[$i] = $result;
    	
					$i++;
				}
				
				mysql_free_result($resource);
				
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
			trigger_error('Error: ' . mysql_error($this->connection) . '<br />Error No: ' . mysql_errno($this->connection) . '<br />' . $sql);
			exit();
    	}
  	}
	
	public function escape($value) {
		return mysql_real_escape_string($value, $this->connection);
	}
	
  	public function countAffected() {
    	return mysql_affected_rows($this->connection);
  	}

  	public function getLastId() {
    	return mysql_insert_id($this->connection);
  	}	
	
	public function __destruct() {
		mysql_close($this->connection);
	}
}
