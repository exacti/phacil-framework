<?php
/**
 * @copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework;

/**
 * PHPDoc Parser 
 * 
 * Simple example usage: 
 * @example $a = new Parser($string); $a->parse();
 * 
 * @package Phacil\Framework
 * @since 2.0.0
 */
class PHPDocParser {
	
	/**
	 * The PHPDoc string that we want to parse
	 * @var string
	 */
	private $string;

	/**
	 * Storge for the short description
	 * @var string
	 */
	private $shortDesc;

	/**
	 * Storge for the long description
	 * 
	 * @var string
	 */
	private $longDesc;

	/**
	 * Storge for all the PHPDoc parameters
	 * 
	 * @var array
	 */
	private $params = [];
	
	/**
	 * Parse each line
	 *
	 * Takes an array containing all the lines in the string and stores
	 * the parsed information in the object properties
	 * 
	 * @param array $lines An array of strings to be parsed
	 */
	private function parseLines($lines) {
		foreach($lines as $line) {
			$parsedLine = $this->parseLine($line); //Parse the line
			
			if($parsedLine === false && empty($this->shortDesc)) {
				if(isset($desc) && is_array($desc))
					$this->shortDesc = implode(PHP_EOL, $desc); //Store the first line in the short description

				$desc = array();
			} elseif($parsedLine !== false) {
				$desc[] = $parsedLine; //Store the line in the long description
			}
		}
		$this->longDesc = implode(PHP_EOL, $desc);
	}

	/**
	 * Parse the line
	 *
	 * Takes a string and parses it as a PHPDoc comment
 	 * 
	 * @param string $line The line to be parsed
	 * @return string|bool|array False if the line contains no parameters or paramaters that aren't valid otherwise, the line that was passed in.
	 */
	private function parseLine($line) {
		
		//Trim the whitespace from the line
		$line = trim($line);
		
		if(empty($line)) return false; //Empty line
		
		if(strpos($line, '@') === 0) {
			$param = substr($line, 1, strpos($line, ' ') - 1); //Get the parameter name
			$value = substr($line, strlen($param) + 2); //Get the value
			if($this->setParam($param, $value)) return false; //Parse the line and return false if the parameter is valid
		}
		
		return $line;
	}

	/**
	 * Setup the valid parameters
	 * 
	 * @param string $type NOT USED
	 */
	private function setupParams($type = "") {
		$params = array(
			"access"	=>	'',
			"author"	=>	'',
			"copyright"	=>	'',
			"deprecated"=>	'',
			"example"	=>	'',
			"ignore"	=>	'',
			"internal"	=>	'',
			"link"		=>	'',
			"param"		=>	'',
			"return"	=> 	'',
			"see"		=>	'',
			"since"		=>	'',
			"tutorial"	=>	'',
			"version"	=>	'',
			'throws'	=>	'',
			'todo'		=> 	''
		);
		
		$this->params = $params;
	}
	
	/**
	 * Parse a parameter or string to display in simple typecast display
	 *
	 * @param string $string The string to parse
 	 * @return string Formatted string wiht typecast
	 */
	private function formatParamOrReturn($string) {
		
		//$pos = strpos($string, ' ');

		$parts = preg_split('/\s+/', $string, 3, PREG_SPLIT_NO_EMPTY);
		
		//$type = substr($string, 0, $pos);

		if(count($parts) == 3) {
			$return = [
				$parts[1] => [
					'type' => (strpos($parts[0], '|')) ? explode('|',$parts[0]) : $parts[0],
					'desc'	=> $parts[2]
				]
			];
		} elseif (count($parts) == 2) {
			$return = [
				$parts[1] => [
					'type' => (strpos($parts[0], '|')) ? explode('|', $parts[0]) : $parts[0]
				]
			];
		} elseif (count($parts) == 1) {
			$return = (strpos($parts[0], '|')) ? explode('|', $parts[0]) : $parts[0];
		} else { $return = '';}

		//return '(' . $type . ')' . substr($string, $pos+1);
		return $return;
	}
	
	/**
	 * Set a parameter
	 * 
	 * @param string $param The parameter name to store
	 * @param string $value The value to set
	 * @return bool True = the parameter has been set, false = the parameter was invalid
	 */
	private function setParam($param, $value)
	{
		if (!array_key_exists($param, $this->params)) $this->params[$param] = '';

		if ($param == 'param' || $param == 'return') $value = $this->formatParamOrReturn($value);

		if (empty($this->params[$param])) {
			$this->params[$param] = $value;
		} elseif (is_array($this->params[$param])) {
			$this->params[$param] = array_merge($this->params[$param], (is_array($value) ? $value : array($value)));
		} elseif (is_string($this->params[$param])) {
			$this->params[$param] = array($this->params[$param], $value);
		}
		return true;
	}

	/**
	 * Setup the initial object
	 * 
	 * @param string $string The string we want to parse
	 */
	public function __construct($string) {
		$this->string = $string;
		//$this->setupParams();
	}

	/**
	 * Parse the string
	 * @return void
	 */
	public function parse() {
		//Get the comment
		if(preg_match('#^/\*\*(.*)\*/#s', $this->string, $comment) === false)
			die("Error");
			
		$comment = trim($comment[1]);
		
		//Get all the lines and strip the * from the first character
		if(preg_match_all('#^\s*\*(.*)#m', $comment, $lines) === false)
			die('Error');
		
		$this->parseLines($lines[1]);
	}

	/**
	 * Get the short description
	 *
	 * @return string The short description
	 */
	public function getShortDesc() {
		return $this->shortDesc;
	}

	/**
	 * Get the long description
	 *
	 * @return string The long description
	 */
	public function getDesc() {
		return $this->longDesc;
	}

	/**
	 * Get the parameters
	 *
	 * @return array The parameters
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @param string $key 
	 * @return mixed 
	 */
	public function get($key){
		return ($this->$key) ?: null;
	}
}
