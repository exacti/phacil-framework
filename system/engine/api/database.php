<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Api;

/**
 * Database interface
 * @api
 * @since 2.0.0
 * @package Phacil\Framework\Api
 */
interface Database {
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
	public function __construct($driver, $hostname, $username, $password, $database);


	/** 
	 * Check is connected on database
	 * @return bool  
	 **/
	public function isConnected();

	/**
	 * Execute the SQL Query
	 * 
	 * @param string|null $sql 
	 * @param bool $cacheUse 
	 * @return \Phacil\Framework\Databases\Object\ResultInterface|\Phacil\Framework\Database::Cache|\Phacil\Framework\MagiQL 
	 * @throws PhpfastcacheInvalidArgumentException 
	 */
	public function query($sql = null, $cacheUse = true);

	/**
	 * Important escape to prevent SQL injection.
	 * 
	 * @param string $value 
	 * @return string 
	 */
	public function escape($value);

	/** 
	 * Gets the number of rows affected by the last operation
	 * 
	 * @return int 
	 */
	public function countAffected();

	/** 
	 * Gets the ID of the last inserted row or sequence of values
	 * 
	 * @return int  
	 */
	public function getLastId();

	/**
	 * @param string $sql 
	 * @param int $pageNum_exibe 
	 * @param int $maxRows_exibe 
	 * @param bool $cache 
	 * @param string|null $sqlTotal 
	 * @return object 
	 * @deprecated 2.0.0 This method as no longer maintained and will be removed on any 2.x further version (not defined yet). 
	 * @deprecated Use MaqiQL class (\Phacil\Framework\MagiQL) instead.
	 * @see \Phacil\Framework\MagiQL To use statement queries for more secure and relialable code.
	 * @throws PhpfastcacheInvalidArgumentException 
	 */
	public function pagination($sql, $pageNum_exibe = 1, $maxRows_exibe = 10, $cache = true, $sqlTotal = null);

	/**
	 * @param string $name 
	 * @param \Phacil\Framework\Api\Database $object 
	 * @return $this 
	 */
	public function createSubBase($name, \Phacil\Framework\Api\Database $object);

	/**
	 * Execute a prepared statement with parameters
	 *
	 * @param string $sql SQL query with named placeholders
	 * @param array $params Associative array of parameters
	 * @return \Phacil\Framework\Databases\Object\ResultInterface|true
	 * @throws \Phacil\Framework\Exception 
	 */
	public function execute($sql, array $params = []);

	/**
	 * Textual database driver type
	 * @return string 
	 */
	public function getDBType();

	/**
	 * ID of database driver
	 * 
	 * @return int 1 = MySQL/MariaDB
	 * @return int 2 = MS SQL Server
	 * @return int 3 = Oracle Database
	 * @return int 4 = Postgre 
	 * @return int 5 = SQLite3
	 * @return int 0 = NULL 
	 */
	public function getDBTypeId();
}
