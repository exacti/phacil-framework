<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Driver;

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

    /** {@inheritdoc} */
    public function __construct($hostname, $username, $password, $database, $charset = 'utf8mb4') {
        //$this->connection = NULL;
    }

    /** {@inheritdoc} */
    public function isConnected() { 

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function query($sql) {
        $result = new \Phacil\Framework\Databases\Object\Result();
        $result->num_rows = NULL;
        $result->row = NULL;
        $result->rows = NULL;
        return $result;
    }

    /** {@inheritdoc} */
    public function escape($value) {
        return NULL;
    }

    /** {@inheritdoc} */
    public function countAffected() {
        return NULL;
    }

    /** {@inheritdoc} */
    public function getLastId() {
        return NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($sql, array $params = [])
    {
        return [null];
    }
}
