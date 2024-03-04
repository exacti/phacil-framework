<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Databases\Api\DriverInterface;

/** @package Phacil\Framework\Databases */
class Postgre implements DriverInterface {

	const DB_TYPE = 'Postgre';

	const DB_TYPE_ID = 4;

	/**
	 * 
	 * @var resource|false
	 */
	private $link;

	/**
	 * {@inheritdoc}
	 */
	public function __construct($hostname, $username, $password, $database, $port = '5432', $charset = 'UTF8') {
		if (!$this->link = pg_connect('host=' . $hostname . ' port=' . $port .  ' user=' . $username . ' password='	. $password . ' dbname=' . $database)) {
			throw new \Phacil\Framework\Exception('Error: Could not make a database link using ' . $username . '@' . $hostname);
		}
		if (!pg_ping($this->link)) {
			throw new \Phacil\Framework\Exception('Error: Could not connect to database ' . $database);
		}
		pg_query($this->link, "SET CLIENT_ENCODING TO '".$charset."'");
	}

	public function isConnected() { 
		return ($this->link) ? true : false;
	}

	/**
	 * {@inheritdoc}
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

				/** @var \Phacil\Framework\Databases\Object\ResultInterface */
				$query = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$data]);
				$query->setNumRows($i);

				unset($data);
				return $query;
			} else {
				return true;
			}
		} else {
			throw new \Phacil\Framework\Exception('Error: ' . pg_result_error($this->link) . '<br />' . $sql);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function escape($value) {
		return pg_escape_string($this->link, $value);
	}

	/** {@inheritdoc} */
	public function countAffected() {
		return pg_affected_rows($this->link);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLastId() {
		$query = $this->query("SELECT LASTVAL() AS `id`");
		return $query->getRow()->getValue('id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute($sql, array $params = [])
	{
		// Verificar se há parâmetros e fazer o bind
		if (!empty($params)) {
			$sql = $this->replacePlaceholders($sql, array_keys($params));
			$result = pg_query_params($this->link, $sql, array_values($params));

			if ($result === false) {
				throw new \Phacil\Framework\Exception('Error executing query: ' . pg_last_error($this->link));
			}

			// Processar resultados se for um SELECT
			$i = 0;
			$data = [];
			while ($row = pg_fetch_assoc($result)) {
				$data[$i] = $row;
				$i++;
			}

			/** @var \Phacil\Framework\Databases\Object\ResultInterface */
			$resultObj = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$data]);
			$resultObj->setNumRows($i);

			return $resultObj;
		} else {
			// Se não há parâmetros, executar diretamente sem consulta preparada
			return $this->query($sql);
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
			$sql = str_replace($placeholder, '$' . ($placeholders[$placeholder] + 1), $sql);
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