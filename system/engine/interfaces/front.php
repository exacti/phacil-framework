<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */


namespace Phacil\Framework\Interfaces;


interface Front {

	/**
	 * @param \Phacil\Framework\Interfaces\Action $pre_action 
	 * @return void 
	 */
	public function addPreAction(\Phacil\Framework\Interfaces\Action $pre_action);

	/**
	 * @param \Phacil\Framework\Interfaces\Action $action 
	 * @param string $error 
	 * @return void 
	 */
	public function dispatch(\Phacil\Framework\Interfaces\Action $action, $error);

 }