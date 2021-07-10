<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

class Url {
	public $baseurl;
	private $url;
	private $ssl;
    public $cdn = false;
	private $hook = array();
	
	public function __construct($url, $ssl) {
		$this->url = $url;
		$this->ssl = $ssl;
        if(defined('CDN')) {
            $this->cdn = CDN;
        } else {
            $this->cdn = false;
        }
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->baseurl = $ssl;
		} else {
			$this->baseurl = $url;
		}
	}
	
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
		
	public function addRewrite($hook) {
		$this->hook[] = $hook;
	}

	public function rewrite($url) {
		foreach ($this->hook as $hook) {
			$url = $hook->rewrite($url);
		}
		
		return $url;		
	}
}
