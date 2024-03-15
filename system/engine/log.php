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
	 * @var \Phacil\Framework\Api\Log[]
	 */
	private $extraLoggers = [];

	/** @var bool */
	protected $removeUsedContextFields = false;

	/**
	 * @var bool
	 */
	protected $allowInlineLineBreaks = true;

	/**
	 * Log output format
	 * @var string
	 */
	protected $format = "[%datetime%] %channel%.%level_name%: %message% %context% %extra% | %route%";

	/**
	 * {@inheritdoc}
	 */
	public function __construct($filename = \Phacil\Framework\Api\Log::DEFAULT_FILE_NAME) {
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
		//$this->fileobj = fopen($this->filepath, 'a+');
		return;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLog($name) {
		if(($name.self::LOG_FILE_EXTENSION) == $this->filename){
			return $this;
		}

		if(isset($this->extraLoggers[$name])){ 
			return $this->extraLoggers[$name];
		}

		return $this->extraLoggers[$name] = new self($name . self::LOG_FILE_EXTENSION);
	}

	/**
	 * {@inheritdoc}
	 */
	public function write($message) {
		return $this->writeToFile('[' . $this->getDateTime() . '] - ' . print_r($message, true)." | ".$_SERVER['REQUEST_URI'] . " | " . \Phacil\Framework\startEngineExacTI::getRoute());
	}

	/**
	 * @param string $message 
	 * @return int|false 
	 */
	protected function writeToFile($message) {
		if(!$this->fileobj){
			$this->fileobj = fopen($this->filepath, 'a+');
		}
		return fwrite($this->fileobj, $message . PHP_EOL);
	}

	/** @return string  */
	protected function getDateTime() {
		if (version_compare(phpversion(), "7.2.0", "<")) {
			// Obtém o timestamp atual com precisão de microssegundos
			$microtime = microtime(true);

			// Arredonda os milissegundos para três casas decimais
			$milissegundos = round(($microtime - floor($microtime)) * 1000);

			return sprintf(date(self::DATE_TIME_FORMAT_PHP5), (string)$milissegundos);
		}

		return (new \DateTimeImmutable())->format(self::DATE_TIME_FORMAT);
	}
	
	/** 
	 * Remove object from memory
	 * 
	 * @since 1.0.0
	 * @return void  
	 */
	public function __destruct() {
		if ($this->fileobj) {
			fclose($this->fileobj);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFileName()
	{
		return $this->filename;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilePath()
	{
		return $this->filepath;
	}

	/**
	 * @inheritdoc
	 */
	public function setLogformat($logformat) {
		$this->format = $logformat;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setRemoveUsedContextFields($remove = false) {
		$this->removeUsedContextFields = $remove;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setAllowInlineLineBreaks($allow = true){
		$this->allowInlineLineBreaks = $allow;
		return $this;
	}

	/**
	 * @inheritdoc
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
	 * @inheritdoc
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

	/**
	 * {@inheritdoc}
	 */
	public function emergency($message, array $context = array())
	{
		return $this->log($message, self::EMERGENCY, $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function alert($message, array $context = array())
	{
		return $this->log($message, self::ALERT, $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function critical($message, array $context = array())
	{
		return $this->log($message, self::CRITICAL, $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function error($message, array $context = array())
	{
		return $this->log($message, self::ERROR, $context);
	}

	/**
	 * @inheritdoc
	 */
	public function warning($message, array $context = array())
	{
		return $this->log($message, self::WARNING, $context);
	}

	/**
	 * @inheritdoc
	 */
	public function notice($message, array $context = array())
	{
		return $this->log($message, self::NOTICE, $context);
	}

	/**
	 * @inheritdoc
	 */
	public function info($message, array $context = array())
	{
		return $this->log($message, self::INFO, $context);
	}

	/**
	 * @inheritdoc
	 */
	public function debug($message, array $context = array())
	{
		return $this->log($message, self::DEBUG, $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function log($message = null, $level = self::INFO, array $context = array()) {
		$record = [
			'message' => $message,
			'context' => $context,
			'level' => $level,
			'level_name' => $level,
			'channel' => str_replace(self::LOG_FILE_EXTENSION, '', $this->filename),
			'datetime' => $this->getDateTime(),
			'extra' => [],
			'route' => \Phacil\Framework\startEngineExacTI::getRoute(),
		];

		return $this->writeToFile($this->interpolate($this->format, $this->logTreatment($record)));
	}

	/**
	 * @param array $record 
	 * @return array 
	 * @throws \RuntimeException 
	 */
	protected function logTreatment(array $record)
	{
		if (false === strpos($record['message'], '{')) {
			return $record;
		}

		try {
			$replacements = [];
			foreach ($record['context'] as $key => $val) {
				$placeholder = '{' . $key . '}';
				if (strpos($record['message'], $placeholder) === false) {
					continue;
				}

				if (is_null($val) || is_scalar($val) || (is_object($val) && method_exists($val, "__toString"))) {
					$replacements[$placeholder] = $val;
				} elseif ($val instanceof \DateTimeInterface) {
					$replacements[$placeholder] = $val->format(self::DATE_TIME_FORMAT);
				} elseif ($val instanceof \UnitEnum) {
					$replacements[$placeholder] = $val instanceof \BackedEnum ? $val->value : $val->name;
				} elseif (is_object($val)) {
					$replacements[$placeholder] = '[object ' . \get_class($val) . ']';
				} elseif (is_array($val)) {
					$replacements[$placeholder] = 'array' . $this->toJson($val);
				} else {
					$replacements[$placeholder] = '[' . gettype($val) . ']';
				}

				if ($this->removeUsedContextFields) {
					unset($record['context'][$key]);
				}
			}

			$record['message'] = strtr($record['message'], $replacements);

		} catch (\Exception $th) {
			//throw $th;
			$this->warning($th->getMessage());
		}
		
		return $record;
	}

	/**
	 * @param mixed $data
	 */
	protected function convertToString($data)
	{
		if (null === $data || is_bool($data)) {
			return var_export($data, true);
		}

		if (is_scalar($data)) {
			return (string) $data;
		}

		return $this->toJson($data);
	}

	/**
	 * Return the JSON representation of a value
	 *
	 * @param  mixed             $data
	 * @throws \RuntimeException if encoding fails and errors are not ignored
	 * @return string            if encoding fails and ignoreErrors is true 'null' is returned
	 */
	protected function toJson($data)
	{
		return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_PARTIAL_OUTPUT_ON_ERROR);;
	}

	/**
	 * @param string $str 
	 * @return string 
	 * @throws \Phacil\Framework\Exception\RuntimeException 
	 */
	protected function replaceNewlines($str)
	{
		if ($this->allowInlineLineBreaks) {
			if (0 === strpos($str, '{')) {
				$str = preg_replace('/(?<!\\\\)\\\\[rn]/', "\n", $str);
				if (null === $str) {
					$pcreErrorCode = preg_last_error();
					throw new \Phacil\Framework\Exception\RuntimeException('Failed to run preg_replace: ' . $pcreErrorCode );
				}
			}

			return $str;
		}

		return str_replace(["\r\n", "\r", "\n"], ' ', $str);
	}

	/**
	 * @param mixed $value
	 */
	protected function stringify($value)
	{
		return $this->replaceNewlines($this->convertToString($value));
	}

	/**
	 * @param mixed $format 
	 * @param mixed $record 
	 * @return string 
	 * @throws \RuntimeException 
	 */
	protected function interpolate($format, $record)
	{
		$replace = [];

		foreach ($record as $key => $value) {
			if (is_array($value)) {
				$value = $this->convertToString($value);
			}
			$replace['%' . $key . '%'] = $value;
		}

		return strtr($format, $replace);
	}
}
