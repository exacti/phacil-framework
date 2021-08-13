<?php

/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 
namespace Phacil\Framework;

class Exception extends \Exception
{

	public function __destruct()
	{
		$log = new \Phacil\Framework\Log("exception.log");
		$log->write($this->getMessage());
	}
	
}
