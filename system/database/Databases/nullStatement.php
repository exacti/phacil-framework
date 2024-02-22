<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Databases\Api\DriverInterface;

/**
 * Nullable fake simulated DB connection.
 * 
 * @package Phacil\Framework\Databases
 */
final class nullStatement implements DriverInterface {
    //private $connection;

    const DB_TYPE = NULL;

    const DB_TYPE_ID = 0;

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

    public function __construct($hostname, $username, $password, $database, $charset = 'utf8mb4') {
        //$this->connection = NULL;
    }

    public function isConnected() { 

        return false;
    }

    /**
     * 
     * @param string $sql 
     * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
     */
    public function query($sql) {
        $result = new \Phacil\Framework\Databases\Object\Result();
        $result->num_rows = NULL;
        $result->row = NULL;
        $result->rows = NULL;
        return $result;
    }

    public function escape($value) {
        return NULL;
    }

    public function countAffected() {
        return NULL;
    }

    public function getLastId() {
        return NULL;
    }

    /**
     * Execute a prepared statement with parameters
     *
     * @param string $sql SQL query with named placeholders
     * @param array $params Associative array of parameters
     * @return null
     * @throws \Phacil\Framework\Exception
     */
    public function execute($sql, array $params = [])
    {
        return [null];
    }
}
