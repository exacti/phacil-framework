<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Connectors\Oracle\ORDS\Helper;

/**
 * @since 2.0.0
 * @package Phacil\Framework\Databases\Connectors\Oracle\ORDS
 */
class Data extends \Phacil\Framework\AbstractHelper {

	/**
	 * @param mixed $value 
	 * @return mixed 
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException 
	 */
	public function escape($value)
	{
		$formattedValue = null;

		switch (gettype($value)) {
			case 'integer':
				$formattedValue = $value; //Integers don't need formatting
				break;
			case 'double':
				$formattedValue = sprintf('%F', $value); //Formats decimal numbers
				break;
			case 'boolean':
				$formattedValue = $value ? 'TRUE' : 'FALSE'; //Convert boolean to string 'TRUE' or 'FALSE'
				break;
			case 'NULL':
				$formattedValue = 'NULL';
				break;
			case 'object':
				$formattedValue = "'" . serialize($value) . "'"; //Serialize the object
				break;
			case 'array':
				$formattedValue = "'" . \Phacil\Framework\Json::encode($value) . "'";
				break;
			default:
				// Escape single quotes and add single quotes around strings
				$formattedValue = "'" . str_replace("'", "''", $value) . "'";
		}

		return $formattedValue;
	}
}