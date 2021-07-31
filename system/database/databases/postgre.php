<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

final class Postgre {
	/**
	 * 
	 * @var resource|false
	 */
	private $link;

	/**
	 * 
	 * @param string $hostname 
	 * @param string $username 
	 * @param string $password 
	 * @param string $database 
	 * @param string $port 
	 * @param string $charset 
	 * @return void 
	 * @throws Exception 
	 */
	public function __construct($hostname, $username, $password, $database, $port = '5432', $charset = 'UTF8') {
		if (!$this->link = pg_connect('host=' . $hostname . ' port=' . $port .  ' user=' . $username . ' password='	. $password . ' dbname=' . $database)) {
			throw new \Exception('Error: Could not make a database link using ' . $username . '@' . $hostname);
		}
		if (!pg_ping($this->link)) {
			throw new \Exception('Error: Could not connect to database ' . $database);
		}
		pg_query($this->link, "SET CLIENT_ENCODING TO '".$charset."'");
	}

	/**
	 * @param string $sql 
	 * @return stdClass|true 
	 * @throws Exception 
	 */
	public function query($sql) {
		$resource = pg_query($this->link, $sql);
		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;
				$data = array();
				while ($result = pg_fetch_assoc($resource)) {
					$data[$i] = $result;
					$i++;
				}
				pg_free_result($resource);
				$query = new \stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;
				unset($data);
				return $query;
			} else {
				return true;
			}
		} else {
			throw new \Exception('Error: ' . pg_result_error($this->link) . '<br />' . $sql);
		}
	}
	
	/**
	 * @param string $value 
	 * @return string 
	 */
	public function escape($value) {
		return pg_escape_string($this->link, $value);
	}
	
	/** @return int  */
	public function countAffected() {
		return pg_affected_rows($this->link);
	}
	
	/**
	 * @return mixed 
	 * @throws Exception 
	 */
	public function getLastId() {
		$query = $this->query("SELECT LASTVAL() AS `id`");
		return $query->row['id'];
	}
	
	/** @return void  */
	public function __destruct() {
		pg_close($this->link);
	}
}