<?php
/*
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */


namespace Phacil\Framework\ArrayClass\Aux; 

abstract class LegacyAux
{
	protected $_container = array();

	/**
	 * Assigns a value to the specified offset
	 *
	 * @param string $offset The offset to assign the value to
	 * @param mixed  $value The value to set
	 * @access public
	 * @abstracting ArrayAccess
	 * 
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		if (is_string($offset)) $offset = strtolower($offset);
		if (is_null($offset)) {
			$this->_container[] = $value;
		} else {
			$this->_container[$offset] = $value;
		}
	}

	/**
	 * Whether or not an offset exists
	 *
	 * @param string $offset An offset to check for
	 * @access public
	 * @return bool
	 * @abstracting ArrayAccess
	 */
	public function offsetExists($offset)
	{
		if (is_string($offset)) $offset = strtolower($offset);
		return isset($this->_container[$offset]);
	}

	/**
	 * Unsets an offset
	 *
	 * @param string $offset The offset to unset
	 * @access public
	 * @abstracting ArrayAccess
	 * 
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		if (is_string($offset)) $offset = strtolower($offset);
		unset($this->_container[$offset]);
	}

	/**
	 * Returns the value at specified offset
	 *
	 * @param string $offset The offset to retrieve
	 * @access public
	 * @return mixed
	 * @abstracting ArrayAccess
	 */
	public function offsetGet($offset)
	{
		if (is_string($offset)) $offset = strtolower($offset);
		return isset($this->_container[$offset])
			? $this->_container[$offset]
			: null;
	}
}