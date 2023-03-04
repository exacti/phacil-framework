<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Interfaces\Databases;
use \SQLite3;
use \stdClass;

final class Sqlite3_db implements Databases {
    /**
     * 
     * @var SQLite3
     */
    private $connection;

    public function __construct($hostname, $username = null, $password = null, $database, $port = '3306', $charset = 'utf8mb4')
    {
        $this->connection = new \SQLite3($hostname.$database, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $password);

        if (!$this->connection) {
            throw new \Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode());
        }
    }

    /**
     * 
     * @param string $sql 
     * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
     * @throws \Exception 
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
            $result = new \Phacil\Framework\Databases\Object\Result();
            $result->setNumRows((!empty($data)) ? count($data) : 0);
            $result->setRow(isset($data[0]) ? $data[0] : array());
            $result->setRows($data);
            $query->finalize();
            return $result;

        } else {
            throw new \Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode() . '<br />' . $sql);
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
}