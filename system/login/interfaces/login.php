<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Login\Interfaces;

use Phacil\Framework\Registry;

/** 
 * Login interface
 * 
 * @since 2.0.0
 * @package Phacil\Framework\Login\Interfaces 
 */
interface Login
{
	/**
	 * @param string $authorizedUsers 
	 * @param Registry|null $registry 
	 * @return void 
	 */
	public function __construct($authorizedUsers, \Phacil\Framework\Registry $registry = null);

	/**
	 * Restrict Access To Page: Grant or deny access to this page
	 * 
	 * @param string $strUsers 
	 * @param string $strGroups 
	 * @param string $UserName 
	 * @param string $UserGroup 
	 * @return bool 
	 */
	public function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup);

	/**
	 * Check if is logged on MM_Username session and, if isn't, go to $restrictGoTo URL
	 * 
	 * @param string $restrictGoTo URL that send the user for login 
	 * @return void 
	 */
	public function check($restrictGoTo);

	/**
	 * Check is logged
	 * 
	 * @return bool 
	 */
	public function isLogged();

	/**
	 * Destroy the login session
	 * 
	 * @return void 
	 */
	public function logout();

	/**
	 * Return the login Username
	 * 
	 * @return string 
	 */
	public function getUserName();
}