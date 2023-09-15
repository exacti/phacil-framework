<?php

/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework\Interfaces;

 interface Databases {

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
	public function query($sql = null);

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
	 * Destroy the connection
	 * 
	 * @return void  */
	public function __destruct();
 }