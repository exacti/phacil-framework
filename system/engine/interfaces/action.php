<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Interfaces;

/**
 * Action interface.
 * 
 * @since 2.0.0
 * @package Phacil\Framework\Interfaces
 */
interface Action {

	/**
	 * @param string $route HTTP route for the respective controller
	 * @param array $args Args to be pass for the method controller
	 * @return void 
	 */
	public function __construct($route, $args = array());

	/** 
	 * Return the controller file path
	 * 
	 * @since 1.0.0
	 * @return string  
	 */
	public function getFile();

	/** 
	 * Return the class of controller
	 * 
	 * @deprecated 2.0.0 This method return only legacy class. Use getClassAlt instead.
	 * @see \Phacil\Framework\Interfaces\Action::getClassAlt()
	 * 
	 * @since 1.0.0
	 * @return string  */
	public function getClass();

	/**
	 * Set the class of controller to be loaded
	 * 
	 * @param string $class 
	 * @return $this 
	 */
	public function setClass($class);

	/** 
	 * Get all possible classes for controller in the new 2.0 framework version
	 * 
	 * @since 2.0.0
	 * 
	 * @return array  
	 */
	public function getClassAlt();

	/** 
	 * Return the method to be loaded
	 * 
	 * @return string  
	 * @since 1.0.0
	 */
	public function getMethod();

	/** 
	 * Return the args
	 * 
	 * @return array  
	 * @since 1.0.0
	 */
	public function getArgs();
}