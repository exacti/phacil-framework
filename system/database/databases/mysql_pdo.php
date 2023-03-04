<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases;

use Phacil\Framework\Interfaces\Databases;

final class MYSQL_PDO implements Databases
{
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
    public function query($sql = null)
    {
        if ($this->dbh) {
            $data = new \Phacil\Framework\Databases\Object\Result();
			
			$sth=$this->dbh->prepare($sql);
			$sth->execute();
            //$sth= $this->dbh->query($sql);
			$this->affectedRows = $sth->rowCount();
            $data->rows         = $sth ? $sth->fetchAll() : array();
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
}