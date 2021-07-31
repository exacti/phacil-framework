<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Log {
	
	/**
	 * 
	 * @var resource|false
	 */
	private $filename;
		
	/**
	 * @param string $filename 
	 * @return void 
	 */
	public function __construct($filename = "error.log") {
		$this->filename = fopen(DIR_LOGS . $filename, 'a+');
		return;
	}
	
	/**
	 * @param string $message 
	 * @return void 
	 */
	public function write($message) {
		fwrite($this->filename, date('Y-m-d G:i:s') . ' - ' . print_r($message, true)." | ".$_SERVER['REQUEST_URI'] . PHP_EOL);
	}
	
	/** @return void  */
	public function __destruct() {
		fclose($this->filename);
	}
}
