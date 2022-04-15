<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
abstract class AbstractHelper {
	
	/**
	 * 
	 * @var Registry
	 */
	protected $registry;
	
	/**
	 * @param Registry $registry 
	 * @return void 
	 */
	public function __construct(Registry $registry = NULL) {
		if (!$registry) {
			/**
			 * @var \Phacil\Framework\Registry
			 */
			$registry = \Phacil\Framework\Registry::getInstance();
		}
		$this->registry = &$registry;
	}

	/** @return void  */
	private function __getRegistryClass()
	{
		$this->registry = \Phacil\Framework\startEngineExacTI::getRegistry();
	}

	/**
	 * 
	 * @param mixed $key 
	 * @return mixed 
	 */
	public function __get($key)
	{
		if (!$this->registry) {
			$this->__getRegistryClass();
		}

		return $this->registry->get($key);
	}

	/**
	 * 
	 * @param mixed $key 
	 * @param mixed $value 
	 * @return void 
	 */
	public function __set($key, $value)
	{
		if (!$this->registry) {
			$this->__getRegistryClass();
		}

		$this->registry->set($key, $value);
	}
}
