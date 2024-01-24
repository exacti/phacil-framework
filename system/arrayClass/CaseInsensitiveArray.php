<?php
/*
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */

namespace Phacil\Framework\ArrayClass;

if (version_compare(phpversion(), "8.0.0", "<")) {

	class supCaseComp extends \Phacil\Framework\ArrayClass\Aux\LegacyAux {
		
	}

} else {
	class supCaseComp extends \Phacil\Framework\ArrayClass\Aux\ModernAux
	{
		
	}

}

class CaseInsensitiveArray extends supCaseComp implements \ArrayAccess
{
	protected $_container = array();

	/**
	 * @param array $initial_array 
	 * @return void 
	 */
	public function __construct(array $initial_array = array())
	{
		//$this->_container = array_map("strtolower", $initial_array);
		$this->_container = array_change_key_case($initial_array);
	}

}