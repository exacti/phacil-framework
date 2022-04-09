<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Config;

class Url {

	/**
	 * 
	 * @var string
	 */
	public $baseurl;

	/**
	 * 
	 * @var string
	 */
	private $url;

	/**
	 * 
	 * @var string
	 */
	private $ssl;

	/**
	 * 
	 * @var bool
	 */
    public $cdn = false;

	/**
	 * 
	 * @var array
	 */
	private $hook = array();
	
	/**
	 * @param string $url 
	 * @param string $ssl 
	 * @return void 
	 */
	public function __construct($url, $ssl) {
		$this->url = $url;
		$this->ssl = $ssl;
		$this->cdn = Config::CDN() ?: false;
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->baseurl = $ssl;
		} else {
			$this->baseurl = $url;
		}
	}
	
	/**
	 * @param string $route 
	 * @param string $args 
	 * @param string $connection 
	 * @return string 
	 */
	public function link($route, $args = '', $connection = 'NONSSL') {
		if ($connection ==  'NONSSL') {
			$url = $this->url;	
		} else {
			$url = $this->ssl;	
		}
		
		$url .= 'index.php?route=' . $route;
		
		if ($args) {
			if (is_array($args)) {
				$url .= '&' . http_build_query($args);
			} else {
				//$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
				$url .= '&' . ltrim($args, '&');
			}
		}
		
		
		return $this->rewrite($url);
	}
		
	/**
	 * @param string $hook 
	 * @return void 
	 */
	public function addRewrite($hook) {
		$this->hook[] = $hook;
	}

	/**
	 * @param string $url 
	 * @return string 
	 */
	public function rewrite($url) {
		foreach ($this->hook as $hook) {
			$url = $hook->rewrite($url);
		}
		
		return $url;		
	}
}
