<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/**
 * Factory Class
 *
 * This class provides functionalities for dynamically and flexibly creating instances of other classes.
 *
 * @since 2.0.0
 * @package Phacil\Framework
 * @api
 */
class Factory {
	/**
	 * @var string|null The name of the class to be instantiated by the factory.
	 */
	protected $class;

	/**
	 * 
	 * Factory constructor.
	 *
	 * @param string|null $class (Optional) The name of the class to be instantiated by the factory.
	 *
	 * @return void 
	 */
	public function __construct($class = null) {
		$this->class = $class;
	}

	/**
	 * Sets the name of the class to be instantiated by the factory.
	 *
	 * @param string $class The name of the class.
	 * @return $this 
	 */
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}

	/** 
	 * Gets the name of the class currently configured to be instantiated by the factory.
	 *
	 * @return string  
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * Creates an instance of the class configured in the factory.
	 *
	 * @param array $args (Optional) Additional arguments for the class constructor.
	 * @return mixed The created instance.
	 * 
	 * @throws \ReflectionException 
	 * @throws \Phacil\Framework\Exception 
	 * @throws \Exception 
	 */
	public function create(array $args = []) {
		return \Phacil\Framework\Registry::getInstance()->create($this->class, $args);
	}
}
