<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Interfaces\Databases;

/** 
 * Principal class to load databases drivers
 * 
 * @package Phacil\Framework */
final class Database {
	/**
	 * 
	 * @var Databases
	 */
	private $driver;
	
	/**
	 * 
	 * @var string
	 */
	private $cachePrefix = "SQL_";
	
	/**
	 * Construct the connection.
	 * 
	 * @param string $driver 
	 * @param string $hostname 
	 * @param string $username 
	 * @param string $password 
	 * @param string $database 
	 * @return void 
	 */
	public function __construct($driver, $hostname, $username, $password, $database) {

        $driverClass = "\\Phacil\\Framework\\Databases\\".$driver;

		try {
            $this->driver = new $driverClass($hostname, $username, $password, $database);
        } catch (Exception $th) {
            throw new Exception('Error: Could not load database file ' . $driver . '!');
            //exit('Error: Could not load database file ' . $driver . '!');
        }		
		
	}

	/** 
	 * Check is connected on database
	 * @return bool  */
	public function isConnected() { 
		return $this->driver->isConnected();
	}

	/** 
	 * Destroy the connection
	 * 
	 * @return void  */
	public function __destruct() {
		unset($this->driver);
	 }
		
  	/**
	 * Execute the SQL Query
	 * 
  	 * @param string $sql 
  	 * @param bool $cacheUse 
  	 * @return object|\Phacil\Framework\Database::Cache 
  	 * @throws PhpfastcacheInvalidArgumentException 
  	 */
  	public function query($sql, $cacheUse = true) {
		
		if(defined('SQL_CACHE') && SQL_CACHE == true && $cacheUse == true) {
			
			return $this->Cache($sql);
			
		} else {
			
			return $this->driver->query($sql);
		}		
		
  	}
	
	/**
	 * Important escape to prevent SQL injection.
	 * 
	 * @param string $value 
	 * @return string 
	 */
	public function escape($value) {
		return $this->driver->escape($value);
	}
	
  	/** 
	 * Gets the number of rows affected by the last operation
	 * 
	 * @return int  */
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	/** 
	 * Gets the ID of the last inserted row or sequence of values
	 * 
	 * @return int|string  */
  	public function getLastId() {
		return $this->driver->getLastId();
  	}

    /**
     * @param string $sql 
     * @param int $pageNum_exibe 
     * @param int $maxRows_exibe 
     * @param bool $cache 
     * @param mixed|null $sqlTotal 
     * @return object 
     * @throws PhpfastcacheInvalidArgumentException 
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
        $totalRows_exibe = (isset($all_exibe->row['__TOTALdeREG_DB_Pagination'])) ? $all_exibe->row['__TOTALdeREG_DB_Pagination'] : $all_exibe->num_rows;

        if($totalRows_exibe <= 0){
            $all_exibe_query = $query_exibe;
            $all_exibe = $this->query($all_exibe_query, $cache);
            $totalRows_exibe = (isset($all_exibe->row['__TOTALdeREG_DB_Pagination'])) ? $all_exibe->row['__TOTALdeREG_DB_Pagination'] : $all_exibe->num_rows;
        }

        $totalPages_exibe = ceil($totalRows_exibe/$maxRows_exibe);

        $exibe->totalPages_exibe = $totalPages_exibe;
        $exibe->totalRows_exibe = $totalRows_exibe;
        $exibe->pageNum_exibe = $pageNum_exibe+1;

        return $exibe;
    }
	
	/**
	 * @param string $sql 
	 * @return object 
	 * @throws PhpfastcacheInvalidArgumentException 
	 */
	private function Cache($sql) {
		if(class_exists('Caches')) {
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
	 * @param string $nome 
	 * @param object $object 
	 * @return void 
	 */
	public function createSubBase($nome, $object) {

        $this->$nome = $object;
    }
}
