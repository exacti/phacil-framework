<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Driver;

use Phacil\Framework\Databases\Api\DriverInterface;
use \SQLite3 as nativeSQLite3;

class SQLite3 implements DriverInterface {

    const DB_TYPE = 'SQLite3';

    const DB_TYPE_ID = 5;

    /**
     * 
     * @var nativeSQLite3
     */
    private $connection;

    /**
     * {@inheritdoc}
     */
    public function __construct($hostname, $username = null, $password = null, $database = null, $port = '3306', $charset = 'utf8mb4')
    {
        $this->connection = new nativeSQLite3($hostname.$database, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $password);

        if (!$this->connection) {
            throw new \Phacil\Framework\Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode());
        }
    }

    /**
     * 
     * @inheritdoc
     */
    public function query($sql){
        //$query = $this->connection->query($sql);

        if ($stm = $this->connection->prepare($sql)) {

            $query = $stm->execute();

            if (!$query instanceof \SQLite3Result || $query->numColumns() == 0)
                return true;


            $data = [];
            while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }

            /** @var \Phacil\Framework\Databases\Object\ResultInterface */
            $result = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Object\ResultInterface::class, [$data]);
            $result->setNumRows((!empty($data)) ? count($data) : 0);

            $query->finalize();
            return $result;

        } else {
            throw new \Phacil\Framework\Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode() . '<br />' . $sql);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function escape($value) {
        return $this->connection->escapeString($value);
    }

    /** {@inheritdoc} */
    public function countAffected() {
        return $this->connection->changes();
    }

    /** {@inheritdoc} */
    public function getLastId() {
        return $this->connection->lastInsertRowID();
    }

    /** {@inheritdoc} */
    public function isConnected() {
        return ($this->connection) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($sql, array $params = [])
    {
        if (!empty($params)) {
            $stmt = $this->connection->prepare($sql);

            if ($stmt === false) {
                throw new \Phacil\Framework\Exception('Error preparing query: ' . $this->connection->lastErrorMsg());
            }

            foreach ($params as $placeholder => &$param) {
                $stmt->bindValue($placeholder, $param, $this->getParamType($param));
            }
        } else {
            $stmt = $this->connection->prepare($sql);
        }

        $result = $stmt->execute();

        if ($result === false) {
            throw new \Phacil\Framework\Exception('Error executing query: ' . $this->connection->lastErrorMsg());
        }

        // Processar resultados se for um SELECT
        if ($result instanceof \SQLite3Result) {
            $data = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }

            /** @var \Phacil\Framework\Databases\Object\ResultInterface */
            $resultObj = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Object\ResultInterface::class, [$data]);
            $resultObj->setNumRows(count($data));

            $result->finalize();

            return $resultObj;
        }

        // Se não for um SELECT, apenas retornar verdadeiro
        return true;
        
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

    private function getParamType(&$param)
    {
        $paramType = gettype($param);

        switch ($paramType) {
            case 'boolean':
            case 'integer':
                $paramType = SQLITE3_INTEGER;
                break;
            case 'double':
            case 'float':
                $paramType = SQLITE3_FLOAT;
                break;
            case 'NULL':
                $paramType = SQLITE3_NULL;
                break;
            default:
                $paramType = SQLITE3_TEXT;
                break;
        }

        return $paramType;
    }
}