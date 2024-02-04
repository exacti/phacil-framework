<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Interfaces;


interface Url
{
	/**
	 * @param string $url 
	 * @param string $ssl 
	 * @return void 
	 */
	public function __construct($url, $ssl);

	/**
	 * @param string $route 
	 * @param string $args 
	 * @param string $connection 
	 * @return string 
	 */
	public function link($route, $args = '', $connection = 'NONSSL');

	/**
	 * @param string $hook 
	 * @return void 
	 */
	public function addRewrite($hook);

	/**
	 * @param string $url 
	 * @return string 
	 */
	public function rewrite($url);
}
