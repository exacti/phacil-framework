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
		if(!$registry){
			
			/**
			 * @global \Phacil\Framework\startEngineExacTI $engine
			 */
			global $engine;
			$registry = $engine->registry;
		}
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
}
