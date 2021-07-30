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
	 * @return mixed 
	 */
	public function model(string $model);

	/**
	 * @param string $helper 
	 * @return mixed 
	 */
	public function helper(string $helper);

	/**
	 * @param mixed $control 
	 * @return mixed 
	 */
	public function control($control);

	/**
	 * @param mixed $control 
	 * @return mixed 
	 */
	public function controller($control);

	/**
	 * @param mixed $driver 
	 * @param mixed $hostname 
	 * @param mixed $username 
	 * @param mixed $password 
	 * @param mixed $database 
	 * @param mixed|null $port 
	 * @param mixed|null $charset 
	 * @return mixed 
	 */
	public function database(string $driver, $hostname, $username, $password, $database, $port = NULL, $charset = NULL);

	/**
	 * @param mixed $config 
	 * @return mixed 
	 */
	public function config($config);

	/**
	 * @param mixed $language 
	 * @return mixed 
	 */
	public function language($language);
 }