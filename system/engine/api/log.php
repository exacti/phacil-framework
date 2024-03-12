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

	const LOG_FILE_EXTENSION = '.log';

	const DEFAULT_FILE_NAME = 'error.log';

	const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s.v P';

	const DATE_TIME_FORMAT_PHP5 = 'Y-m-d\TH:i:s.%\s P';

	const EMERGENCY = 'EMERGENCY';
	CONST ALERT     = 'ALERT';
	CONST CRITICAL  = 'CRITICAL';
	CONST ERROR     = 'ERROR';
	CONST WARNING   = 'WARNING';
	CONST NOTICE    = 'NOTICE';
	CONST INFO      = 'INFO';
	CONST DEBUG     = 'DEBUG';

	/**
	 * @param string $filename (optional) The name of log file. The path is automatic defined in the DIR_LOGS constant in config file. Isn't not possible to change the path. The default name is error.log.
	 * @return void 
	 */
	public function __construct($filename = "error.log");

	/**
	 * @param string $name 
	 * @return $this|\Phacil\Framework\Api\Log 
	 */
	public function setLog($name);

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

	/**
	 * System is unusable.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function emergency($message, array $context = array());

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function alert($message, array $context = array());

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function critical($message, array $context = array());

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function error($message, array $context = array());

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function warning($message, array $context = array());

	/**
	 * Normal but significant events.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function notice($message, array $context = array());

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function info($message, array $context = array());

	/**
	 * Detailed debug information.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function debug($message, array $context = array());

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param string  $level
	 * @param string $message
	 * @param array  $context (Optional)
	 *
	 * @return int|false 
	 *
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException
	 */
	public function log($level, $message, array $context = array());
}