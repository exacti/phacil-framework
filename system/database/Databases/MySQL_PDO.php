<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Interfaces\Databases;

/** @package Phacil\Framework\Databases */
class MySQL_PDO implements Databases
{

    const DB_TYPE = 'MySQL';

    const DB_TYPE_ID = self::LIST_DB_TYPE_ID['MYSQL'];

    /**
     * Link to the database connection
     *
     * @var \PDO
     */
    private $dbh;
    /**
     * List of connection settings
     *
     * @var array
     */
    private $options = array(
        'PDO::ATTR_ERRMODE' => \PDO::ERRMODE_SILENT
    );
    /**
     * The number of rows affected by the last operation
     *
     * @var int
     */
    private $affectedRows = 0;
    /**
     * The data for the database connection
     *
     * @var \stdClass
     */
    private $params = array();
    /**
     * Sets the connection and connects to the database
     *
     * @param string $host server Address
     * @param string $user Username
     * @param string $pass Password
     * @param string $name The database name
     * @param string $charset Encoding connection
     */
    public function __construct($host, $user, $pass, $name, $port = '3306', $charset = 'utf8mb4')
    {
        $this->params = new \stdClass;
        # keep connection data
        $this->params->host    = $host;
        $this->params->user    = $user;
        $this->params->pass    = $pass;
        $this->params->name    = $name;
        $this->params->charset = $charset;
        $this->params->connstr = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
        # add the connection parameters
        $this->options['PDO::MYSQL_ATTR_INIT_COMMAND'] = "SET NAMES '{$charset}'";
        $this->connect();
    }

    public function isConnected() { 
        if ($this->dbh) {
			return true;
		} else {
			return false;
		}
    }
    /**
     * Connect to database
     */
    public function connect()
    {
        try {
            $this->dbh = new \PDO($this->params->connstr, $this->params->user, $this->params->pass, $this->options);
            if (version_compare(PHP_VERSION, '5.3.6', '<=')) {
                $this->dbh->exec($this->options['PDO::MYSQL_ATTR_INIT_COMMAND']);
            }
        } catch (\PDOException $exception) {
            throw new \Phacil\Framework\Exception($exception->getMessage());
        }
    }
    
    /**
     * Query the database
     * @param string $sql 
     * @return \Phacil\Framework\Databases\Object\ResultInterface|true 
     * @throws \PDOException 
     */
    public function query($sql)
    {
        if ($this->dbh) {
            $data = new \Phacil\Framework\Databases\Object\Result();
			
			$sth=$this->dbh->prepare($sql);
			$sth->execute();
            //$sth= $this->dbh->query($sql);
			$this->affectedRows = $sth->rowCount();
            $data->rows         = $sth ? $sth->fetchAll(\PDO::FETCH_ASSOC) : array();
            $data->row          = isset($data->rows[0]) ? $data->rows[0] : null;
            $data->num_rows     = $this->affectedRows;
            return $data;
        }
        return null;
    }
    /**
     * Concludes the string in quotation marks to be used in the query
     *
     * @param mixed $string shielded line
     * @return string Returns shielded line or to FALSE , if the driver does not support screening
     */
    public function escape($string = null)
    {
        return $this->dbh ? str_replace("'", "", $this->dbh->quote($string)) : null;
    }
    /**
     * Gets the number of rows affected by the last operation
     *
     * @return int
     */
    public function countAffected()
    {
        return $this->affectedRows;
    }
    /**
     * Gets the ID of the last inserted row or sequence of values
     *
     * @return int
     */
    public function getLastId()
    {
        return $this->dbh ? $this->dbh->lastInsertId() : 0;
    }
    /**
     * Gets the name of the driver
     *
     * @return string|null
     */
    public function getDriverName()
    {
        return $this->dbh ? $this->dbh->getAttribute(\PDO::ATTR_DRIVER_NAME) : null;
    }
    /**
     * Get information about the version of the client libraries that are used by the PDO driver
     *
     * @return string|null
     */
    public function getVersion()
    {
        return $this->dbh ? $this->dbh->getAttribute(\PDO::ATTR_CLIENT_VERSION) : null;
    }
    /**
     * Closing a database connection
     */
    public function close()
    {
        $this->dbh = null;
    }
    public function __destruct()
    {
        $this->close();
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
            $stmt = $this->dbh->prepare($sql);

            if ($stmt === false) {
                throw new \Phacil\Framework\Exception('Error preparing query: ' . implode(', ', $this->dbh->errorInfo()));
            }

            // Bind parameters if there are any
            if (!empty($params)) {
                foreach ($params as $placeholder => &$param) {
                    $stmt->bindParam($placeholder, $param, $this->getParamType($param));
                }
            }

            $stmt->execute();

            if ($stmt->columnCount()) {
                $data = new \Phacil\Framework\Databases\Object\Result();
                $data->setNumRows($stmt->rowCount());
                $data->setRows($stmt->fetchAll(\PDO::FETCH_ASSOC));
                //$data->setRow(isset($data->rows[0]) ? $data->rows[0] : null);
                $stmt->closeCursor();

                return $data;
            } else {
                $this->affectedRows = $stmt->rowCount();
                $stmt->closeCursor();

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