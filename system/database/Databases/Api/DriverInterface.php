<?php

/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework\Databases\Api;

 /**
  * @since 2.0.0
  * @package Phacil\Framework\Databases\Api
  * @api
  */
 interface DriverInterface {

	const LIST_DB_TYPE_ID = [
		"NULL" => 0,
		"MYSQL" => 1,
		"MSSQL" => 2,
		"ORACLE" => 3,
		"POSTGRE" => 4,
		"SQLLITE3" => 5,
	];

	/**
	 * Construct the connection.
	 * 
	 * @param string $hostname 
	 * @param string $username 
	 * @param string $password 
	 * @param string $database 
	 * @param string $port Is optional
	 * @param string $charset  Is optional
	 * @return void 
	 * @throws Exception 
	 */
	public function __construct($hostname, $username, $password, $database);

	/**
	 * Execute the SQL Query.
	 * 
	 * @param string|null $sql 
	 * @return \Phacil\Framework\Databases\Object\ResultInterface|\Phacil\Framework\MagiQL|bool 
	 * @throws Exception 
	 */
	public function query($sql);

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
	 * @return int  */
	public function countAffected();

	/** 
	 * Gets the ID of the last inserted row or sequence of values
	 * 
	 * @return int|string  */
	public function getLastId();

	/** 
	 * Check is connected on database
	 * @return bool  */
	public function isConnected();

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
	 * Return textual database driver type
	 * 
	 * @return string 
	 */
	public function getDBType();

	/**
	 * Return ID of database type
	 * 
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