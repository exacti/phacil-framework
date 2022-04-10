<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Interfaces\Databases;
use \stdClass;

final class SQLSRV implements Databases {
    /**
     * 
     * @var resource
     */
    private $link;

    /**
     * @param string $hostname 
     * @param string $username 
     * @param string $password 
     * @param string $database 
     * @param string $port 
     * @param string $charset 
     * @return void 
     */
    public function __construct($hostname, $username, $password, $database, $port = '1443', $charset = 'utf8') {
        /*
        * Argument 2 passed to sqlsrv_connect() must be an array, string given
        */
        $connectionInfo = array (
            "UID" => $username,
            "PWD" => $password,
            "Database" => $database
        );

        if (!$this->link = \sqlsrv_connect($hostname, $connectionInfo)) {
            exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
        }

        /*
        if (!mssql_select_db($database, $this->link)) {
        exit('Error: Could not connect to database ' . $database);
        }
        */

        sqlsrv_query($this->link,"SET NAMES 'utf8'");
        sqlsrv_query($this->link, "SET CHARACTER SET utf8");
    }

    public function isConnected() {
        return ($this->link) ? true : false;
    }

    /**
     * @param string $sql 
     * @return stdClass|true 
     */
    public function query($sql) {
        $resource = \sqlsrv_query($this->link, $sql);

        if ($resource) {
            if (is_resource($resource)) {
                $i = 0;

                $data = array();

                while ($result = \sqlsrv_fetch_array($resource, \SQLSRV_FETCH_ASSOC)) {
                    $data[$i] = $result;

                    $i++;
                }

                \sqlsrv_free_stmt($resource);

                $query = new \stdClass();
                $query->row = isset($data[0]) ? $data[0] : array();
                $query->rows = $data;
                $query->num_rows = $i;

                unset($data);

                return $query;
            } else {
                return true;
            }
        } else {
            throw new \Phacil\Framework\Exception('Error: <br />' . $sql);
        }
    }

    /**
     * @param string $value 
     * @return string 
     */
    public function escape($value) {
        $unpacked = unpack('H*hex', $value);

        return '0x' . $unpacked['hex'];
    }

    /** @return int  */
    public function countAffected() {
        return \sqlsrv_rows_affected($this->link);
    }

    /** @return false|int  */
    public function getLastId() {
        $last_id = false;

        $resource = \sqlsrv_query($this->link, "SELECT @@identity AS id");

        if ($row = \sqlsrv_fetch($resource)) {
            $last_id = trim($row[0]);
        }

        sqlsrv_free_stmt($resource);

        return $last_id;
    }

    /** @return void  */
    public function __destruct() {
        \sqlsrv_close($this->link);
    }
}