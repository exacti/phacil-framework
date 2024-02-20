<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Cookies;

/**
 * @since 2.0.0
 * @package Phacil\Framework\Cookies
 */
class SameSite {

	const STRICT = 'Strict';

	const LAX = 'Lax';

	const NONE = 'None';

	private $value = self::STRICT;

	public function setStrict(){
		$this->value = self::STRICT;
		return $this;
	}

	public function setLax(){
		$this->value = self::LAX;
		return $this;
	}

	public function setNone(){
		$this->value = self::NONE;
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	/**
	 * 
	 * @return $this
	 */
	static public function getInstance()
	{
		$class = get_called_class();
		return \Phacil\Framework\Registry::getAutoInstance((new $class()));
	}
}