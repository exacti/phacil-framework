<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

/**
 * Nullable fake simulated DB connection.
 * 
 * @package Phacil\Framework\Databases
 */
final class nullStatement {
    //private $connection;

    public function __construct($hostname, $username, $password, $database, $charset = 'utf8mb4') {
        //$this->connection = NULL;
    }

    public function query($sql) {
        $result = new \stdClass();
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

    public function __destruct() {
        return NULL;
    }
}
