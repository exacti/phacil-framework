<?php
/**
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework;

use Phacil\Framework\Interfaces\Databases as DatabaseInterface;
use Phacil\Framework\Config;
use Phacil\Framework\Api\Database as DatabaseApi;

/** 
 * Principal class to load databases drivers
 * 
 * @since 2.0.0
 * @package Phacil\Framework */
class Database implements DatabaseApi {
	/**
	 * Loaded class db driver
	 * 
	 * @var DatabaseInterface
	 */
	private $driver;

	/**
	 * Legacy config drivers correspondent classes
	 * 
	 * @var string[]
	 */
	static public $legacyDrivers = [
		'mpdo' 			=> '\Phacil\Framework\Databases\mPDO',
		'mysql' 		=> '\Phacil\Framework\Databases\MySQL',
		'dbmysqli' 		=> '\Phacil\Framework\Databases\DBMySQLi',
		'mssql' 		=> '\Phacil\Framework\Databases\MSSQL',
		'mysql_legacy' 	=> '\Phacil\Framework\Databases\MySQL_legacy',
		'mysql_pdo' 	=> '\Phacil\Framework\Databases\MySQL_PDO',
		'mysqli' 		=> '\Phacil\Framework\Databases\MySQLi',
		'nullstatement' => '\Phacil\Framework\Databases\nullStatement',
		'oracle' 		=> '\Phacil\Framework\Databases\Oracle',
		'postgre' 		=> '\Phacil\Framework\Databases\Postgre',
		'sqlite3_db' 	=> '\Phacil\Framework\Databases\SQLite3',
		'sqlsrv' 		=> '\Phacil\Framework\Databases\SQLSRV',
		'sqlsrvpdo' 	=> '\Phacil\Framework\Databases\sqlsrvPDO'
	];
	
	/**
	 * Prefix for query cache
	 * 
	 * @var string
	 */
	private $cachePrefix = "SQL_";

	/**
	 * {@inheritdoc}
	 */
	public function __construct($driver, $hostname, $username, $password, $database) {

        $driverClass = (isset(self::$legacyDrivers[strtolower($driver)])) ? self::$legacyDrivers[strtolower($driver)] : $driver;

		try {
            $this->createDriver(new $driverClass($hostname, $username, $password, $database));
        } catch (\Exception $th) {
            throw new \Phacil\Framework\Exception($driver. ' not loaded. '.$th->getMessage(), $th->getCode());
        }		
		
	}

	/**
	 * @param DatabaseInterface $driverObject 
	 * @return never 
	 * @throws Exception 
	 */
	private function createDriver(DatabaseInterface $driverObject)
	{
		try {
            $this->driver = $driverObject;
        } catch (Exception $th) {
            throw new Exception('Error: Could not create the driver! '.$th->getMessage());
        }
	}

	/**
	 * {@inheritdoc}
	 */
	public function isConnected() { 
		return $this->driver->isConnected();
	}

	/**
	 * {@inheritdoc}
	 */
	public function __destruct() {
		unset($this->driver);
	 }

	/**
	 * {@inheritdoc}
	 */
  	public function query($sql = null, $cacheUse = true) {
		if(!$sql) {
			return new \Phacil\Framework\MagiQL($this);
		}
		
		if(Config::SQL_CACHE() && $cacheUse == true) {
			
			return $this->Cache($sql);
			
		} else {
			
			return $this->driver->query($sql);
		}		
		
  	}

	/**
	 * {@inheritdoc}
	 */
	public function escape($value) {
		return $this->driver->escape($value);
	}

	/**
	 * {@inheritdoc}
	 */
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

	/**
	 * {@inheritdoc}
	 */
  	public function getLastId() {
		return $this->driver->getLastId();
  	}

	/**
	 * {@inheritdoc}
	 */
    public function pagination($sql, $pageNum_exibe = 1, $maxRows_exibe = 10, $cache = true, $sqlTotal = null){

        if (($pageNum_exibe >= 1)) {
            $pageNum_exibe = $pageNum_exibe-1;
        }
        $startRow_exibe = $pageNum_exibe * $maxRows_exibe;

        $query_exibe = $sql;

        $query_limit_exibe = sprintf("%s LIMIT %d, %d", $query_exibe, $startRow_exibe, $maxRows_exibe);

        $exibe = $this->query($query_limit_exibe, $cache);

        $re = '/^(SELECT \*)/i';

        $all_exibe_query = ($sqlTotal != null) ? $sqlTotal : ((preg_match($re, $query_exibe)) ? preg_replace($re, "SELECT COUNT(*) as __TOTALdeREG_DB_Pagination", $query_exibe) : $query_exibe);

        $all_exibe = $this->query($all_exibe_query, $cache);
        $totalRows_exibe = (isset($all_exibe->row['__TOTALdeREG_DB_Pagination'])) ? $all_exibe->row['__TOTALdeREG_DB_Pagination'] : $all_exibe->getNumRows();

        if($totalRows_exibe <= 0){
            $all_exibe_query = $query_exibe;
            $all_exibe = $this->query($all_exibe_query, $cache);
            $totalRows_exibe = (isset($all_exibe->row['__TOTALdeREG_DB_Pagination'])) ? $all_exibe->row['__TOTALdeREG_DB_Pagination'] : $all_exibe->getNumRows();
        }

        $totalPages_exibe = ceil($totalRows_exibe/$maxRows_exibe);

		$final = new \StdClass();

		$final->totalPages_exibe = $totalPages_exibe;
		$final->totalRows_exibe = $totalRows_exibe;
		$final->pageNum_exibe = $pageNum_exibe+1;
		$final->rows = $exibe->getRows();
		$final->row = $exibe->getRow();

        return $final;
    }
	
	/**
	 * @param string $sql 
	 * @return object 
	 * @throws PhpfastcacheInvalidArgumentException 
	 */
	private function Cache($sql) {
		if(class_exists('\Phacil\Framework\Caches')) {
			$cache = new Caches();
		
			if (stripos($sql, "select") !== false) {

				if($cache->check($this->cachePrefix.md5($sql))) {
					
					return $cache->get($this->cachePrefix.md5($sql));
					
				} else {
					$cache->set($this->cachePrefix.md5($sql), $this->driver->query($sql));

					return $this->driver->query($sql);
				}
			} else {
				return $this->driver->query($sql);
			}
		} else {
			return $this->driver->query($sql);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function createSubBase($nome, $object) {

        $this->$nome = $object;
    }

	/**
	 * {@inheritdoc}
	 */
	public function execute($sql, array $params = [])
	{
		return $this->driver->execute($sql, $params);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDBType() {
		return $this->driver->getDBType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDBTypeId() {
		return $this->driver->getDBTypeId();
	}
}
