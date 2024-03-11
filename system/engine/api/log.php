<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Api;

/** 
 * The principal log class for this framework
 * 
 * @param string $filename (optional) The name of log file. The path is automatic defined in the DIR_LOGS constant in config file. Isn't not possible to change the path. The default name is error.log.
 * @package Phacil\Framework 
 * @since 2.0.0
 * @api
 */
interface Log
{
	const DIR_LOGS_PERMISSIONS = 0764;

	const CONFIGURATION_LOGS_CONST = 'DIR_LOGS';

	/**
	 * @param string $filename (optional) The name of log file. The path is automatic defined in the DIR_LOGS constant in config file. Isn't not possible to change the path. The default name is error.log.
	 * @return void 
	 */
	public function __construct($filename = "error.log");

	/**
	 * Write the error message in the log file
	 * 
	 * @param string $message 
	 * @return int|false
	 * @since 1.0.0
	 */
	public function write($message);


	/**
	 * Return the log file name
	 * 
	 * @since 2.0.0
	 * @return string 
	 */
	public function getFileName();

	/**
	 * Return the log file path
	 * 
	 * @since 2.0.0
	 * @return string 
	 */
	public function getFilePath();

	/**
	 * Return last lines of log archive
	 * 
	 * @param string|null $filepath (optional) Path of file. Default is the log file.
	 * @param int $lines (optional) Lines to be readed. Default value is 10.
	 * @param bool $adaptive (optional) Default value is true.
	 * @return false|string 
	 * @since 2.0.0
	 */
	public function tail($filepath = null, $lines = 10, $adaptive = true);

	/**
	 * Return first lines of log archive
	 * 
	 * @param string|null $filepath (optional) Path of file. Default is the log file.
	 * @param int $lines (optional) Lines to be readed. Default value is 10.
	 * @param bool $adaptive (optional) Default value is true.
	 * @return false|string 
	 * @since 2.0.0
	 */
	public function head($filepath = null, $lines = 10, $adaptive = true);
}