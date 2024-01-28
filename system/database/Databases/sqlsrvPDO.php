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

class sqlsrvPDO implements Databases {

    const DB_TYPE = 'Microsoft SQL Server Database';

    const DB_TYPE_ID = 2;

    /**
     * 
     * @var PDONative
     */
    private $connection = null;

    /**
     * 
     * @var PDOStatement
     */
    private $statement = null;

    /**
     * 
     * @var int
     */
    private $affectedRows = 0;

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
    public function __construct($hostname, $username, $password, $database, $port = '3306', $charset = 'utf8') {
        try {
            $this->connection = new PDONative("sqlsrv:Server=" . $hostname . ";Database=" . $database, $username, $password, array(\PDO::SQLSRV_ATTR_DIRECT_QUERY => true));
        } catch(\PDOException $e) {
            throw new \Phacil\Framework\Exception('Failed to connect to database. Reason: \'' . $e->getMessage() . '\'');
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
     * 
     * @param string $sql 
     * @param array $params 
     * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
     * @throws \Phacil\Framework\Exception 
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
            return $this->affectedRows;
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
        try {
            $this->statement = $this->connection->prepare($sql);

            if ($this->statement === false) {
                throw new \Phacil\Framework\Exception('Error preparing query: ' . implode(', ', $this->connection->errorInfo()));
            }

            // Bind parameters if there are any
            if (!empty($params)) {
                foreach ($params as $placeholder => &$param) {
                    $this->statement->bindParam($placeholder, $param, $this->getParamType($param));
                }
            }

            $this->statement->execute();

            if ($this->statement->columnCount()) {
                $data = new \Phacil\Framework\Databases\Object\Result();
                $data->setNumRows($this->statement->rowCount());
                $data->setRows($this->statement->fetchAll(\PDO::FETCH_ASSOC));
                $this->statement->closeCursor();

                return $data;
            } else {
                $this->affectedRows = $this->statement->rowCount();
                $this->statement->closeCursor();

                return true;
            }
        } catch (\PDOException $exception) {
            throw new \Phacil\Framework\Exception($exception->getMessage());
        }
    }

    /**
     * 
     * @param mixed $param 
     * @return int 
     */
    private function getParamType(&$param)
    {
        $paramType = gettype($param);

        switch ($paramType) {
            case 'boolean':
                $paramType = \PDO::PARAM_BOOL;
                break;
            case 'integer':
                $paramType = \PDO::PARAM_INT;
                break;
            case 'double':
            case 'float':
                $paramType = \PDO::PARAM_STR;
                break;
            case 'NULL':
                $paramType = \PDO::PARAM_NULL;
                break;
            default:
                $paramType = \PDO::PARAM_STR;
                break;
        }

        return $paramType;
    }

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
}