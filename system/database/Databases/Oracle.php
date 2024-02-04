<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Interfaces\Databases;

/**
 * Oracle driver connector
 * 
 * @package Phacil\Framework\Databases
 */
class Oracle implements Databases {

	const DB_TYPE = 'Oracle';

	const DB_TYPE_ID = 3;

	/**
	 * 
	 * @var resource|false
	 */
	private $connection;

	/**
	 * 
	 * @var array|false
	 */
	protected $error;

	
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
		$this->connection = \oci_connect($username, $password, $hostname.":".$port."/".$database, $charset);
        if (!$this->connection) {
            $e = \oci_error();
            $this->error = $e;
            throw new \Phacil\Framework\Exception((htmlentities($e['message'], ENT_QUOTES)));
        }
        \oci_set_client_info($this->connection, "Administrator");
        //oci_set_module_name($this->connection, $module);
        //oci_set_client_identifier($this->connection, $cid);

	}
	
	/**
	 * 
	 * @param string $sql 
	 * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function query($sql) {
        $stid = \oci_parse($this->connection, $sql);
        \oci_execute($stid);
		if (!$this->connection) {
            \oci_fetch_all($stid, $res);

			/** @var \Phacil\Framework\Databases\Object\ResultInterface */
            $result = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$res]);
            $result->setNumRows(\oci_num_rows($stid));

            return $result;


		} else {
			throw new \Phacil\Framework\Exception('Error: ' . oci_error()   . '<br />' . $sql);
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
        \oci_close($this->connection);
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
			$sql = $this->replacePlaceholders($sql, array_keys($params));

			$stid = \oci_parse($this->connection, $sql);

			foreach ($params as $placeholder => &$param) {
				\oci_bind_by_name($stid, $placeholder, $param);
			}

			$result_exec = \oci_execute($stid);

			if ($result_exec === false) {
				$e = \oci_error($stid);
				throw new \Phacil\Framework\Exception('Error executing query: ' . htmlentities($e['message'], ENT_QUOTES));
			}

			// Processar resultados se for um SELECT
			$res = [];
			\oci_fetch_all($stid, $res);

			/** @var \Phacil\Framework\Databases\Object\ResultInterface */
			$resultObj = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$res]);
			$resultObj->setNumRows(\oci_num_rows($stid));

			return $resultObj;
		} else {
			// Se não há parâmetros, executar diretamente sem consulta preparada
			$query = $this->query($sql);
			return $query;
		}
	}
	
	/**
	 * Replace placeholders in the SQL query with named placeholders
	 *
	 * @param string $sql SQL query with named placeholders
	 * @param array $placeholders Array of named placeholders
	 * @return string SQL query with named placeholders
	 */
	private function replacePlaceholders($sql, $placeholders)
	{
		foreach ($placeholders as $placeholder) {
			$sql = str_replace($placeholder, ':' . $placeholder, $sql);
		}

		return $sql;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDBType() { 
		return self::DB_TYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDBTypeId() {
		return self::DB_TYPE_ID;
	 }
} 