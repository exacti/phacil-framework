<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Response {

	/**
	 * 
	 * @var array
	 */
	private $headers = array(); 

	/**
	 * 
	 * @var int
	 */
	private $level = 0;

	private $output;
	
	/**
	 * @param string $header 
	 * @return void 
	 */
	public function addHeader($header) {
		$this->headers[] = $header;
	}

	/**
	 * @param string $url 
	 * @return never 
	 */
	public function redirect($url) {
		header('Location: ' . $url);
		exit;
	}
	
	/**
	 * @param int $level 
	 * @return void 
	 */
	public function setCompression($level) {
		$this->level = $level;
	}
		
	/**
	 * @param mixed $output 
	 * @param bool $isJSON 
	 * @param int $HTTPCODE 
	 * @param string|null $HTTPDESC 
	 * @return void 
	 */
	public function setOutput($output, $isJSON = false, $HTTPCODE = 0, $HTTPDESC = null) {

		if($isJSON)
			$this->isJSON();

		if($HTTPCODE !== 0)
			$this->code($HTTPCODE, $HTTPDESC);

		$this->output = $output;
	}

	/**
	 * @param mixed $data 
	 * @param int $level 
	 * @return string|false 
	 */
	private function compress($data, $level = 0) {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
			$encoding = 'gzip';
		} 

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding)) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status()) { 
			return $data;
		}
		
		$this->addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}

	/** @return void  */
	public function output() {
		if ($this->output) {
			if ($this->level) {
				$ouput = $this->compress($this->output, $this->level);
			} else {
				$ouput = $this->output;
			}	
				
			if (!headers_sent()) {
				foreach ($this->headers as $header) {
					header($header, true);
				}
			}
			
			echo $ouput;
		}
	}

	/** @return void  */
	public function isJSON() {
		$this->addHeader('Content-Type: application/json');
	}

	/**
	 * @param int $code 
	 * @param string|null $description 
	 * @return void 
	 */
	public function code(int $code, string $description = null){
		$this->addHeader("HTTP/1.1 ".$code.(($description) ? " ". $description : ""));
        $this->addHeader("Status: ".$code."");
	}
}