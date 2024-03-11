<?php
/**
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework;

use Phacil\Framework\Databases\Api\DriverInterface as DatabaseInterface;
use Phacil\Framework\Config;
use Phacil\Framework\Api\Database as DatabaseApi;
use Phacil\Framework\Exception;

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
		'mpdo' 			=> 'Phacil\Framework\Databases\Driver\mPDO',
		'mysql' 		=> 'Phacil\Framework\Databases\Driver\MySQL',
		'dbmysqli' 		=> 'Phacil\Framework\Databases\Driver\DBMySQLi',
		'mssql' 		=> 'Phacil\Framework\Databases\Driver\MSSQL',
		'mysql_legacy' 	=> 'Phacil\Framework\Databases\Driver\MySQL_legacy',
		'mysql_pdo' 	=> 'Phacil\Framework\Databases\Driver\MySQL_PDO',
		'mysqli' 		=> 'Phacil\Framework\Databases\Driver\MySQLi',
		'nullstatement' => 'Phacil\Framework\Databases\Driver\nullStatement',
		'oracle' 		=> 'Phacil\Framework\Databases\Driver\Oracle',
		'postgre' 		=> 'Phacil\Framework\Databases\Driver\Postgre',
		'sqlite3_db' 	=> 'Phacil\Framework\Databases\Driver\SQLite3',
		'sqlsrv' 		=> 'Phacil\Framework\Databases\Driver\SQLSRV',
		'sqlsrvpdo' 	=> 'Phacil\Framework\Databases\Driver\sqlsrvPDO'
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
			$this->createDriver(
				\Phacil\Framework\Registry::getInstance()->create($driverClass, [
					$hostname,
					$username,
					$password,
					$database
				])
			);
		} catch (\Exception $th) {
			throw new \Phacil\Framework\Exception($driver . ' not loaded. ' . $th->getMessage(), $th->getCode());
		}
	}

	/**
	 * @param DatabaseInterface $driverObject 
	 * @return void 
	 * @throws Exception 
	 */
	private function createDriver(DatabaseInterface $driverObject)
	{
		try {
            $this->driver = $driverObject;
        } catch (\Exception $th) {
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
  	public function query($sql = null, $cacheUse = true) {
		if(!$sql) {
			return \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\MagiQL::class, [$this]);
		}

		if (Config::DB_LOG_ALL()) {
			$this->logQuery($sql, ['cacheUse' => $cacheUse]);
		}
		
		if(Config::SQL_CACHE() && $cacheUse == true) {
			return $this->Cache($sql);
		} 
		
		return $this->driver->query($sql);
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
	 * 
	 * @param string $sql 
	 * @return mixed 
	 * @throws \Phacil\Framework\Exception 
	 * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException 
	 */
	private function Cache($sql) {
		/**
		 * @var \Phacil\Framework\Caches
		 */
		$cache = \Phacil\Framework\Registry::getInstance(Caches::class);

		if($cache->check($this->cachePrefix.md5($sql))) {
			return $cache->get($this->cachePrefix.md5($sql));	
		}
		
		$query = $this->driver->query($sql);
		if($query instanceof \Phacil\Framework\Databases\Api\Object\ResultInterface){
			$cache->set($this->cachePrefix.md5($sql), $query);

			return $query;
		}
		
		return $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createSubBase($name, DatabaseApi $object) {
        $this->$name = $object;
		return $this;
    }

	public function get($name){
		return $this->$name;
	}

	public function id($name) { 
		return $this->get($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute($sql, array $params = [])
	{
		if(Config::DB_LOG_ALL()) {
			$this->logQuery($sql, $params);
		}
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

	/**
	 * @param string $sql 
	 * @param array $params 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	protected function logQuery($sql, $params = array()) {
		if (Config::DB_LOG_ALL()) {
			/** @var \Phacil\Framework\Databases\Api\LogInterface */
			$logger = \Phacil\Framework\Registry::getInstance(\Phacil\Framework\Databases\Api\LogInterface::class, [\Phacil\Framework\Databases\Api\LogInterface::LOG_FILE_NAME]);
			
			$debugging = (\Phacil\Framework\Config::DEBUG()) ?: false;
			$errorFormat = \Phacil\Framework\Config::DEBUG_FORMAT() ?: 'txt';

			$debugStamp = [
				'sql' => $sql,
				'parameters' => $params,
				//'file' => $this->getFile(),
				'trace' => ($debugging) ? \Phacil\Framework\Debug::backtrace(true, false) : null
			];

			$logger->write(($errorFormat == 'json') ? json_encode($debugStamp) : implode(PHP_EOL, array_map(
				[\Phacil\Framework\Exception::class, 'convertArray'],
				$debugStamp,
				array_keys($debugStamp)
			)));
		}
	}
}
