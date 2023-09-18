<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Interfaces\Common;


/** 
 * @since 2.0.0
 * @property \Phacil\Framework\Database $db 
 * @property \Phacil\Framework\Session $session
 * @property \Phacil\Framework\Request $request
 * @property \Phacil\Framework\Url $url
 * @property \Phacil\Framework\Mail $mail
 * @property \Phacil\Framework\Loader $loader
 * @property \Phacil\Framework\startEngineExacTI $engine 
 * @property \Phacil\Framework\Config $config
 * @property \Phacil\Framework\Log $log
 * @property \Phacil\Framework\Caches $cache
 * @property \Phacil\Framework\Response $response
 * @property \Phacil\Framework\Document $document
 * @property \Phacil\Framework\Front $front
 * @property \Phacil\Framework\Translate $translate
 * @package Phacil\Framework\Interfaces 
 */
interface Registers {

	/**
	 * 
	 * @return $this
	 */
	static public function getInstance();

}