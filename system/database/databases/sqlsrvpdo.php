<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use \PDO as PDONative;
use Phacil\Framework\Interfaces\Databases;

final class sqlsrvPDO implements Databases {
    /**
     * 
     * @var PDONative
     */
    private $connection = null;

    /**
     * 
     * @var PDONative
     */
    private $statement = null;

    public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'utf8') {
        try {
            $this->connection = new PDONative("sqlsrv:Server=" . $hostname . ";Database=" . $database, $username, $password, array(\PDO::SQLSRV_ATTR_DIRECT_QUERY => true));
        } catch(\PDOException $e) {
            throw new \Exception('Failed to connect to database. Reason: \'' . $e->getMessage() . '\'');
        }


    }

    /**
     * @param string $sql 
     * @return void 
     */
    public function prepare($sql) {
        $this->statement = $this->connection->prepare($sql);
    }

    /**
     * @param mixed $parameter 
     * @param mixed $variable 
     * @param int $data_type 
     * @param int $length 
     * @return void 
     */
    public function bindParam($parameter, $variable, $data_type = \PDO::PARAM_STR, $length = 0) {
        if ($length) {
            $this->statement->bindParam($parameter, $variable, $data_type, $length);
        } else {
            $this->statement->bindParam($parameter, $variable, $data_type);
        }
    }

    /**
     * @return never 
     * @throws Exception 
     */
    public function execute() {
        try {
            if ($this->statement && $this->statement->execute()) {
                $data = array();

                while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                $result = new \stdClass();
                $result->row = (isset($data[0])) ? $data[0] : array();
                $result->rows = $data;
                $result->num_rows = $this->statement->rowCount();
            }
        } catch(\PDOException $e) {
            throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode());
        }
    }

    /**
     * 
     * @param string $sql 
     * @param array $params 
     * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
     * @throws \Exception 
     */
    public function query($sql, $params = array()) {
        $this->statement = $this->connection->prepare($sql);

        $result = false;

        try {
            if ($this->statement && $this->statement->execute($params)) {
                $data = array();

                while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                $result = new \Phacil\Framework\Databases\Object\Result();
                $result->setRow((isset($data[0])) ? $data[0] : array());
                $result->setRows($data);
                $result->setNumRows($this->statement->rowCount());
            }
        } catch (\PDOException $e) {
            throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode() . ' <br />' . $sql);
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

    /**
     * @param string $value 
     * @return string 
     */
    public function escape($value) {
        return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
    }

    /** @return int  */
    public function countAffected() {
        if ($this->statement) {
            return $this->statement->rowCount();
        } else {
            return 0;
        }
    }

    /** @return string  */
    public function getLastId() {
        return $this->connection->lastInsertId();
    }

    /** @return bool  */
    public function isConnected() {
        if ($this->connection) {
            return true;
        } else {
            return false;
        }
    }

    /** @return void  */
    public function __destruct() {
        unset($this->connection);
    }
}