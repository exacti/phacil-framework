<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** 
 * Login class
 * 
 * You can extend if need.
 * 
 * @since 1.0.0
 * @package Phacil\Framework 
 */
class Login implements \Phacil\Framework\Login\Interfaces\Login {
	
	/**
	 * 
	 * @var array
	 */
	private $MM_authorizedUsers = array();

	/**
	 * 
	 * @var string
	 */
	private $MM_donotCheckaccess = "false";

	/**
	 * 
	 * @var \Phacil\Framework\Registry|null
	 */
	private $engine = null;
	
	/**
	 * @inheritdoc
	 */
	public function __construct($authorizedUsers, \Phacil\Framework\Registry $registry = null){
		$this->MM_authorizedUsers = $authorizedUsers;
		if (!$registry) {

			/**
			 * @var \Phacil\Framework\Registry
			 */
			$registry = \Phacil\Framework\Registry::getInstance();
		}
		$this->registry = &$registry;
		$this->engine =& $registry;
		
	}

	/**
	 * Restrict Access To Page: Grant or deny access to this page
	 * 
	 * @param string $strUsers 
	 * @param string $strGroups 
	 * @param string $UserName 
	 * @param string $UserGroup 
	 * @return bool 
	 */
	public function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
		
		// For security, start by assuming the visitor is NOT authorized. 
		$isValid = false; 
		
		// When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
		// Therefore, we know that a user is NOT logged in if that Session variable is blank. 
		if (!empty($UserName)) { 
			// Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
			// Parse the strings into arrays. 
			$arrUsers = explode(",", $strUsers); 
			$arrGroups = explode(",", $strGroups); 
			if (in_array($UserName, $arrUsers)) {
				$isValid = true; 
			} 
			// Or, you may restrict access to only certain users based on their username. 
			if (in_array($UserGroup, $arrGroups)) { 
				$isValid = true; 
			} 
			/* if (($strUsers == "") && false) { 
				$isValid = true; 
			}  */
		} 
		return $isValid; 
	}

	/**
	 * @param string $restrictGoTo 
	 * @return void 
	 * @inheritdoc
	 */
	public function check($restrictGoTo) {
				
		$MM_restrictGoTo = $restrictGoTo;
		
		if (!((isset($this->engine->session->data['MM_Username'])) && ($this->isAuthorized("",$this->MM_authorizedUsers, $this->engine->session->data['MM_Username'], $this->engine->session->data['MM_UserGroup'])))) {
			$MM_qsChar = "?";
			$MM_referrer = Request::SERVER('PHP_SELF');
			if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
			if (Request::SERVER('QUERY_STRING') && strlen(Request::SERVER('QUERY_STRING')) > 0) 
				$MM_referrer .= "?" . Request::SERVER('QUERY_STRING');
			$MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
			header("Location: ". $MM_restrictGoTo); 
			exit;
		}
	}

	/** @inheritdoc  */
	public function isLogged () {
		$lgged = $this->isAuthorized("",$this->MM_authorizedUsers, $this->engine->session->data['MM_Username'], $this->engine->session->data['MM_UserGroup']);
		
		return($lgged);
	}

	/** @inheritdoc  */
	public function logout() {
		unset($this->engine->session->data['MM_Username']);
		unset($this->engine->session->data['MM_UserGroup']);
		
		session_destroy();
  	}

	/** @inheritdoc  */
	public function getUserName() {
    	return $this->engine->session->data['MM_Username'];
  	}

	/** @inheritdoc  */
	public function getUserGroup() {
    	return $this->engine->session->data['MM_UserGroup'];
  	}
	
}