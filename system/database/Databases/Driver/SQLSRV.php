<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Databases\Driver;

use Phacil\Framework\Databases\Api\DriverInterface;

/** @package Phacil\Framework\Databases */
class SQLSRV implements DriverInterface {

    const DB_TYPE = 'Microsoft SQL Server Database';

    const DB_TYPE_ID = self::LIST_DB_TYPE_ID['MSSQL'];

    /**
     * 
     * @var resource
     */
    private $link;

    /**
     * {@inheritdoc}
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
            throw new \Phacil\Framework\Exception('Error: Could not make a database connection using ' . $username . '@' . $hostname);
        }

        /*
        if (!mssql_select_db($database, $this->link)) {
        exit('Error: Could not connect to database ' . $database);
        }
        */

        \sqlsrv_query($this->link,"SET NAMES 'utf8'");
        \sqlsrv_query($this->link, "SET CHARACTER SET utf8");
    }

    public function isConnected() {
        return ($this->link) ? true : false;
    }

    /**
     * {@inheritdoc}
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

                /** @var \Phacil\Framework\Databases\Object\ResultInterface */
                $query = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Object\ResultInterface::class, [$data]);
                $query->setNumRows($i);

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
     * @inheritdoc
     */
    public function escape($value) {
        $unpacked = unpack('H*hex', $value);

        return '0x' . $unpacked['hex'];
    }

    /** @inheritdoc */
    public function countAffected() {
        return \sqlsrv_rows_affected($this->link);
    }

    /** @inheritdoc  */
    public function getLastId() {
        $last_id = false;

        $resource = \sqlsrv_query($this->link, "SELECT @@identity AS id");

        if ($row = \sqlsrv_fetch($resource)) {
            $last_id = trim($row[0]);
        }

        sqlsrv_free_stmt($resource);

        return $last_id;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($sql, array $params = [])
    {
        if (!empty($params)) {
            foreach ($params as $placeholder => &$param) {

                //$stmt->bind_param($this->getParamType($param), $param);
                $bindParams[] = &$param;

                if (is_string($placeholder))
                    $sql = str_replace($placeholder, '?', $sql);
            }
            // Bind parameters if there are any
            $stmt = \sqlsrv_prepare($this->link, $sql, $bindParams);
        } else {
             $stmt = \sqlsrv_prepare($this->link, $sql);
        }

        if ($stmt === false) {
            throw new \Phacil\Framework\Exception('Error preparing query: ' . \sqlsrv_errors());
        }

        $result = \sqlsrv_execute($stmt);

        if ($result === false) {
            $errors = \sqlsrv_errors();
            throw new \Phacil\Framework\Exception('Error executing query: ' . json_encode($errors));
        }

        // Processar resultados se for um SELECT
        if (\sqlsrv_has_rows($stmt)) {
            $data = [];
            while ($row = \sqlsrv_fetch_array($stmt, \SQLSRV_FETCH_ASSOC)) {
                $data[] = $row;
            }

            /** @var \Phacil\Framework\Databases\Object\ResultInterface */
            $resultObj = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Object\ResultInterface::class, [$data]);
            $resultObj->setNumRows(\sqlsrv_num_rows($stmt));

            return $resultObj;
        }

        // Se não for um SELECT, apenas retornar verdadeiro
        return $result;
        
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