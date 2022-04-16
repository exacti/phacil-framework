<?php
/**
 * Copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */


namespace Phacil\Framework;

use Phacil\Framework\Interfaces\Serializer;


/**
 * Serialize data to JSON, unserialize JSON encoded data
 *
 * @api
 * @since 2.0.0
 */
class Json implements Serializer
{
	/**
	 * Encode data into string
	 *
	 * @param string|int|float|bool|array|null $data
	 * @return string|bool
	 * @throws \InvalidArgumentException
	 * @since 2.0.0
	 */
	static public function encode($data)
	{
		$result = json_encode($data);
		if (false === $result) {
			throw new \InvalidArgumentException("Unable to serialize value. Error: " . self::json_last_error_msg());
		}
		return $result;
	}

	/**
	 * Decode the given string
	 *
	 * @param string $string
	 * @return string|int|float|bool|array|null
	 * @throws \InvalidArgumentException
	 * @since 2.0.0
	 */
	static public function decode($string, $array = true)
	{
		$result = json_decode($string, $array);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \InvalidArgumentException("Unable to unserialize value. Error: " . self::json_last_error_msg());
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function serialize( $data){
		return self::encode($data);
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function unserialize($data){
		return self::decode($data);
	}

	static private function fallbackErrorMsg()
	{
		//$JSON_ERROR_RECURSION = (defined('JSON_ERROR_RECURSION') ? JSON_ERROR_RECURSION : 6);
		$ERRORS = array(
			JSON_ERROR_NONE =>	'No error has occurred',
			JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
			JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
			JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
			JSON_ERROR_SYNTAX => 'Syntax error',
			JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
			Config::JSON_ERROR_RECURSION(6) => 'One or more recursive references in the value to be encoded',
			Config::JSON_ERROR_INF_OR_NAN(7) => 'One or more NAN or INF values in the value to be encoded',
			Config::JSON_ERROR_UNSUPPORTED_TYPE(8) => 'A value of a type that cannot be encoded was given',
			Config::JSON_ERROR_INVALID_PROPERTY_NAME(9) => 'A property name that cannot be encoded was given',
			Config::JSON_ERROR_UTF16(10) => 'Malformed UTF-16 characters, possibly incorrectly encoded'
		);

		$error = json_last_error();
		return function_exists('json_last_error_msg') ? json_last_error_msg() : (isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error');
	}

	/**
	 * json_last_error_msg compatibility function
	 * 
	 * @return string 
	 * @since 2.0.0
	 */
	static public function json_last_error_msg()
	{
		return function_exists('json_last_error_msg') ? json_last_error_msg() : self::fallbackErrorMsg();
	}
}
