<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Exception;
use Phacil\Framework\Interfaces\Databases;
use stdClass;

/**
 * Oracle driver connector
 * 
 * @package Phacil\Framework\Databases
 */
final class Oracle implements Databases {
	/**
	 * 
	 * @var resource|false
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
	public function __construct($hostname, $username, $password, $database, $port = '1521', $charset = 'utf8') {
		$this->connection = oci_connect($username, $password, $hostname."/".$database, $charset);
        if (!$this->connection) {
            $e = oci_error();
            $this->error = $e;
            throw new \Exception(trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR));
        }
        oci_set_client_info($this->connection, "Administrator");
        //oci_set_module_name($this->connection, $module);
        //oci_set_client_identifier($this->connection, $cid);

	}
	
	/**
	 * @param string $sql 
	 * @return stdClass 
	 * @throws Exception 
	 */
	public function query($sql) {
        $stid = oci_parse($this->connection, $sql);
        oci_execute($stid);
		if (!$this->connection) {
            oci_fetch_all($stid, $res);

            $result = new \stdClass();
            $result->num_rows = oci_num_rows($stid);
            $result->row = isset($res[0]) ? $res[0] : array();
            $result->rows = $res;

            return $result;


		} else {
			throw new \Exception('Error: ' . oci_error()   . '<br />' . $sql);
		}
	}
	
	public function escape($value) {
		return str_replace("'", "", $value);
	}
	
	public function countAffected() {
		return NULL;
	}
	public function getLastId() {
		return NULL;
	}
	
	public function isConnected() {
		return $this->connection;
	}
	
	public function __destruct() {
        oci_close($this->connection);
	}
} 