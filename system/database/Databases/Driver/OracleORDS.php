<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Driver;

use Phacil\Framework\Databases\Api\DriverInterface;

/** 
 * Alternative ORDS connection method.
 * 
 * @package Phacil\Framework\Databases */
class OracleORDS implements DriverInterface
{

	const DB_TYPE = 'Oracle';

	const DB_TYPE_ID = self::LIST_DB_TYPE_ID['ORACLE'];

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
	 * 
	 * @var \Phacil\Framework\Databases\Conectors\Oracle\ORDS\Conector
	 */
	private $connection = null;

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Conectors\Oracle\ORDS\Model\Query
	 */
	private $statement = null;

	private $rowCount;

	/** {@inheritdoc} */
	public function __construct($hostname, $username, $password, $database, $port = '8181', $charset = 'UTF8')
	{
		try {
			/** @var \Phacil\Framework\Databases\Conectors\Oracle\ORDS\Conector */
			$this->connection = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Conectors\Oracle\ORDS\Conector::class, [
				$hostname.$database,
				\Phacil\Framework\Config::DB_PORT() ?: $port,
				$username,
				$password
			]);

		} catch (\PDOException $e) {
			throw new \Phacil\Framework\Exception('Failed to connect to database. Reason: \'' . $e->getMessage() . '\'');
		}
	}

	/** {@inheritdoc} */
	public function query($sql, $params = array())
	{
		$this->createStatement();
		$result = false;
		try {
			$data = $this->statement->prepareSQL($sql, $params);
			if ($this->statement && $this->statement->execute($params)) {
				$data = array();
				$this->rowCount = $this->statement->rowCount();
				if ($this->rowCount > 0) {
					try {
						$data = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
					} catch (\Exception $ex) {
					}
				}
				// free up resources
				$this->statement->closeCursor();
				$this->statement = null;

				/** @var \Phacil\Framework\Databases\Api\Object\ResultInterface */
				$result = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Api\Object\ResultInterface::class, [$data]);
				$result->setNumRows($this->rowCount);
			}
		} catch (\PDOException $e) {
			throw new \Phacil\Framework\Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode() . ' <br />' . $sql);
		}
		if ($result) {
			return $result;
		} else {
			$result = new \Phacil\Framework\Databases\Object\Result();
			$result->row = array();
			$result->rows = array();
			$result->num_rows = 0;
			return $result;
		}
	}

	public function createStatement() {
		$this->rowCount = 0;

		/** @var \Phacil\Framework\Databases\Conectors\Oracle\ORDS\Model\Query */
		$this->statement = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Conectors\Oracle\ORDS\Model\Query::class);
	}

	/** {@inheritdoc} */
	public function escape($value)
	{
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

	/** {@inheritdoc} */
	public function countAffected()
	{
		return $this->rowCount;
	}

	/** {@inheritdoc} */
	public function getLastId()
	{
		return $this->connection->lastInsertId();
	}

	/** {@inheritdoc} */
	public function isConnected()
	{
		if ($this->connection) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute($sql, array $params = [])
	{
		$this->createStatement();
		try {
			$stmt = $this->statement->prepareSQL($sql, $params)->execute();

			if (!$stmt) {
				throw new \Phacil\Framework\Exception('Error preparing query: ' . implode(', ', $this->connection->errorInfo()));
			}

			if ($stmt->getNumRows() && !empty($stmt->getItems())) {
				/** @var \Phacil\Framework\Databases\Api\Object\ResultInterface */
				$data = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Api\Object\ResultInterface::class, [$stmt->getItems()]);
				$data->setNumRows($stmt->getNumRows());

				$this->rowCount = $stmt->getNumRows();
				
				$stmt->close();

				return $data;
			} else {
				$this->rowCount = $stmt->getNumRows();
				$stmt->close();
				return true;
			}
		} catch (\PDOException $exception) {
			throw new \Phacil\Framework\Exception($exception->getMessage());
		}
	}
}
