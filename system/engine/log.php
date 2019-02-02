<?php
final class Log {
	private $filename;
		
	public function __construct($filename) {
		$this->filename = fopen(DIR_LOGS . $filename, 'a');
	}
	
	public function write($message) {
		fwrite($this->filename, date('Y-m-d G:i:s') . ' - ' . print_r($message, true)." | ".$_SERVER['REQUEST_URI'] . PHP_EOL);
	}
	
	public function __destruct() {
		fclose($this->filename);
	}
}
?>