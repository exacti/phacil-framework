<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework\Interfaces;

 interface Loader {
	/**
	 * @param string $key 
	 * @return void 
	 */
	public function __get($key);

	/**
	 * @param string $key 
	 * @param object $value 
	 * @return void 
	 */
	public function __set($key, $value);

	/**
	 * @param string $library 
	 * @return void 
	 */
	public function library($library);

	/**
	 * @param string $model 
	 * @return void 
	 */
	public function model(string $model);

	/**
	 * @param string $helper 
	 * @return void 
	 */
	public function helper(string $helper);

	/**
	 * @param string $control 
	 * @return void 
	 */
	public function control($control);

	/**
	 * @param string $control 
	 * @return void 
	 */
	public function controller($control);

	/**
	 * @param string $driver 
	 * @param string $hostname 
	 * @param string $username 
	 * @param string $password 
	 * @param string $database 
	 * @param int|null $port 
	 * @param string|null $charset 
	 * @return string[]|string|null 
	 */
	public function database(string $driver, $hostname, $username, $password, $database, $port = NULL, $charset = NULL);

	/**
	 * @param string $config 
	 * @return void 
	 */
	public function config($config);

	/**
	 * @param string $language 
	 * @return void 
	 */
	public function language($language);
 }