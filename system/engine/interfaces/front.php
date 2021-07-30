<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */


namespace Phacil\Framework\Interfaces;

use Phacil\Framework\Action;
use Phacil\Framework\ActionSystem;

interface Front {

	/**
	 * @param ActionSystem $pre_action 
	 * @return void 
	 */
	public function addPreAction(\Phacil\Framework\ActionSystem $pre_action);

	/**
	 * @param Action $action 
	 * @param string $error 
	 * @return void 
	 */
	public function dispatch(\Phacil\Framework\Action $action, $error);

 }