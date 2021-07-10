<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework;
 
final class Registry {
	private $data = array();

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : NULL);
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function has($key) {
    	return isset($this->data[$key]);
  	}
}
