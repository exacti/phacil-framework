<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Interfaces\Databases;
use \SQLite3 as nativeSQLite3;
use \stdClass;

class SQLite3 implements Databases {

    const DB_TYPE = 'SQLite3';

    const DB_TYPE_ID = 5;

    /**
     * 
     * @var nativeSQLite3
     */
    private $connection;

    /**
     * 
     * @param string $hostname 
     * @param string $username 
     * @param string $password 
     * @param string $database 
     * @param string $port 
     * @param string $charset 
     * @return void 
     * @throws \Phacil\Framework\Exception 
     */
    public function __construct($hostname, $username = null, $password = null, $database, $port = '3306', $charset = 'utf8mb4')
    {
        $this->connection = new nativeSQLite3($hostname.$database, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $password);

        if (!$this->connection) {
            throw new \Phacil\Framework\Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode());
        }
    }

    /**
     * 
     * @param string $sql 
     * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
     * @throws \Phacil\Framework\Exception 
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
            $result = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$data]);
            $result->setNumRows((!empty($data)) ? count($data) : 0);

            $query->finalize();
            return $result;

        } else {
            throw new \Phacil\Framework\Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode() . '<br />' . $sql);
        }

    }

    /**
     * @param string $value 
     * @return string 
     */
    public function escape($value) {
        return $this->connection->escapeString($value);
    }

    /** @return int  */
    public function countAffected() {
        return $this->connection->changes();
    }
    
    /** @return int  */
    public function getLastId() {
        return $this->connection->lastInsertRowID();
    }

    /** @return bool  */
    public function isConnected() {
        return ($this->connection) ? true : false;
    }

    /** @return void  */
    public function __destruct() {
        $this->connection->close();
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
        if (!empty($params)) {
            // Bind parameters if there are any
            foreach ($params as $placeholder => &$param) {
                $sql = str_replace($placeholder, ':' . $placeholder, $sql);
            }

            $stmt = $this->connection->prepare($sql);

            if ($stmt === false) {
                throw new \Phacil\Framework\Exception('Error preparing query: ' . $this->connection->lastErrorMsg());
            }

            foreach ($params as $placeholder => &$param) {
                $stmt->bindValue(':' . $placeholder, $param);
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
                $resultObj = \Phacil\Framework\Registry::getInstance()->create("Phacil\Framework\Databases\Object\ResultInterface", [$data]);
                $resultObj->setNumRows(count($data));

                $result->finalize();

                return $resultObj;
            }

            // Se não for um SELECT, apenas retornar verdadeiro
            return true;
        } else {
            // Se não há parâmetros, executar diretamente sem consulta preparada
            return $this->query($sql);
        }
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