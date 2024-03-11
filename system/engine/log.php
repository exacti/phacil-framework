<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** 
 * The principal log class for this framework
 * 
 * @param string $filename (optional) The name of log file. The path is automatic defined in the DIR_LOGS constant in config file. Isn't not possible to change the path. The default name is error.log.
 * @package Phacil\Framework 
 */
class Log implements \Phacil\Framework\Api\Log {
	
	/**
	 * Storage the object of log file
	 * 
	 * @var resource|false
	 */
	private $fileobj;

	/**
	 * Storage the filename
	 * 
	 * @var string
	 */
	private $filename;

	/**
	 * Storage the path
	 * 
	 * @var string
	 */
	private $filepath;

	/**
	 * @param string $filename (optional) The name of log file. The path is automatic defined in the DIR_LOGS constant in config file. Isn't not possible to change the path. The default name is error.log.
	 * @return void 
	 */
	public function __construct($filename = "error.log") {
		if(!defined(self::CONFIGURATION_LOGS_CONST)){
			if(!defined('DIR_APPLICATION'))
				trigger_error('The '.self::CONFIGURATION_LOGS_CONST.' folder configuration is required!', E_USER_ERROR);

			define(self::CONFIGURATION_LOGS_CONST, DIR_APPLICATION . 'logs/');
		}
		$this->filename = $filename;
		$this->filepath = constant(self::CONFIGURATION_LOGS_CONST) . $filename;
		if(!is_dir(constant(self::CONFIGURATION_LOGS_CONST))){
			$old = umask(0);
			mkdir(constant(self::CONFIGURATION_LOGS_CONST), self::DIR_LOGS_PERMISSIONS, true);
			umask($old);
		}
		if(!is_writable(constant(self::CONFIGURATION_LOGS_CONST))){
			trigger_error('The '. constant(self::CONFIGURATION_LOGS_CONST).' folder must to be writeable!', E_USER_ERROR);
		}
		$this->fileobj = fopen($this->filepath, 'a+');
		return;
	}

	/**
	 * Write the error message in the log file
	 * 
	 * @param string $message 
	 * @return int|false
	 * @since 1.0.0
	 */
	public function write($message) {
		return fwrite($this->fileobj, date('Y-m-d G:i:s') . ' - ' . print_r($message, true)." | ".$_SERVER['REQUEST_URI'] . PHP_EOL);
	}
	
	/** 
	 * Remove object from memory
	 * 
	 * @since 1.0.0
	 * @return void  
	 */
	public function __destruct() {
		fclose($this->fileobj);
	}

	/**
	 * Return the log file name
	 * 
	 * @since 2.0.0
	 * @return string 
	 */
	public function getFileName()
	{
		return $this->filename;
	}

	/**
	 * Return the log file path
	 * 
	 * @since 2.0.0
	 * @return string 
	 */
	public function getFilePath()
	{
		return $this->filepath;
	}

	/**
	 * Return last lines of log archive
	 * 
	 * @param string|null $filepath (optional) Path of file. Default is the log file.
	 * @param int $lines (optional) Lines to be readed. Default value is 10.
	 * @param bool $adaptive (optional) Default value is true.
	 * @return false|string 
	 * @since 2.0.0
	 */
	public function tail($filepath = null, $lines = 10, $adaptive = true)
	{

		// Open file
		$f = @fopen(($filepath ?: $this->filepath), "rb");
		if ($f === false) return false;

		// Sets buffer size
		if (!$adaptive) 
			$buffer = 4096;
		else 
			$buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		// Jump to last character
		fseek($f, -1, SEEK_END);

		// Read it and adjust line number if necessary
		// (Otherwise the result would be wrong if file doesn't end with a blank line)
		if (fread($f, 1) != "\n") $lines -= 1;

		// Start reading
		$output = '';
		$chunk = '';

		// While we would like more
		while (ftell($f) > 0 && $lines >= 0) {

			// Figure out how far back we should jump
			$seek = min(ftell($f), $buffer);

			// Do the jump (backwards, relative to where we are)
			fseek($f, -$seek, SEEK_CUR);

			// Read a chunk and prepend it to our output
			$output = ($chunk = fread($f, $seek)) . $output;

			// Jump back to where we started reading
			fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

			// Decrease our line counter
			$lines -= substr_count($chunk, "\n");
		}

		// While we have too many lines
		// (Because of buffer size we might have read too many)
		while ($lines++ < 0) {

			// Find first newline and remove all text before that
			$output = substr($output, strpos($output, "\n") + 1);
		}

		// Close file and return
		fclose($f);
		return trim($output);
	}

	/**
	 * Return first lines of log archive
	 * 
	 * @param string|null $filepath (optional) Path of file. Default is the log file.
	 * @param int $lines (optional) Lines to be readed. Default value is 10.
	 * @param bool $adaptive (optional) Default value is true.
	 * @return false|string 
	 * @since 2.0.0
	 */
	public function head($filepath = null, $lines = 10, $adaptive = true)
	{

		// Open file
		$f = @fopen(($filepath ?: $this->filepath), "rb");
		if ($f === false) return false;

		// Sets buffer size
		/* if (!$adaptive) 
			$buffer = 4096;
		else 
			$buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096)); */

		// Start reading
		$output = '';
		while (($line = fgets($f)) !== false) {
			if (feof($f)) break;
			if ($lines-- == 0) break;
			$output .= $line . "";
		}

		// Close file and return
		fclose($f);
		return trim($output);
	}
}
