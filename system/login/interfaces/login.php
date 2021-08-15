<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Login\Interfaces;

interface Login
{
	public function __construct($authorizedUsers, \Phacil\Framework\Registry $registry = null);

	public function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup);

	public function check($restrictGoTo);

	public function isLogged();

	public function logout();

	public function getUserName();
}