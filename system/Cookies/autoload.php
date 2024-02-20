<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Cookies\SameSite;
use Phacil\Framework\Cookies\Config as CookieConfig;

/**
 * @since 2.0.0
 * @package Phacil\Framework
 */
class Cookies {

	/**
	 * 
	 * @var int
	 */
	private $expiry;

	/**
	 * 
	 * @var string
	 */
	private $path;

	/**
	 * 
	 * @var string
	 */
	private $domain;

	/**
	 * 
	 * @var bool
	 */
	private $secure;

	/**
	 * 
	 * @var bool
	 */
	private $httpOnly;

	/**
	 * 
	 * @var string
	 */
	private $sameSite;

	/**
	 * 
	 * @var \Phacil\Framework\Cookies\Config
	 */
	private $config;

	private $cookie_key = null;

	private $cookie_value = null;

	/**
	 * @param \Phacil\Framework\Cookies\Config $config 
	 * @return void 
	 */
	public function __construct(
		CookieConfig $config
	){
		$this->config = $config;
	}

	/**
	 * 
	 * @param string $name The name of the cookie.
	 * @param mixed $value The value of the cookie. This value is stored on the clients computer, do not store sensitive information like passwords, personal docs ids, etc...
	 * @param bool $isRaw (Optional) Send a cookie without urlencoding the cookie value
	 * @return bool If output exists prior to calling this function, setcookie will fail and return false. If setcookie successfully runs, it will return true. This does not indicate whether the user accepted the cookie.
	 */
	public function setCookie($name, $value, $isRaw = false)
	{
		if (version_compare(phpversion(), "7.3.0", "<")) {
			if($isRaw){
				return setrawcookie($name, $value, $this->getExpires(), $this->getPath() . "; samesite=".$this->getSameSite(), $this->getDomain(), $this->getSecure(), $this->getHttpOnly());
			}
			return setcookie($name, $value, $this->getExpires(), $this->getPath(). "; samesite=".$this->getSameSite(), $this->getDomain(), $this->getSecure(), $this->getHttpOnly());
		} else {
			if ($isRaw) {
				return setrawcookie($name, $value, [
					'expires' 	=> $this->getExpires(), 
					'path'		=> $this->getPath(),
					'domain' 	=> $this->getDomain(), 
					'secure' 	=> $this->getSecure(),
					'httponly' 	=> $this->getHttpOnly(),
					'samesite'	=> $this->getSameSite()
				]);
			}
			return setcookie($name, $value, [
				'expires' 	=> $this->getExpires(),
				'path'		=> $this->getPath(),
				'domain' 	=> $this->getDomain(),
				'secure' 	=> $this->getSecure(),
				'httponly' 	=> $this->getHttpOnly(),
				'samesite'	=> $this->getSameSite()
			]);
		}
		//return $this;
	}

	/**
	 * 
	 * @param string $name 
	 * @return bool 
	 */
	public function cookieExists($name)
	{
		return isset($_COOKIE[$name]);
	}

	/**
	 * @param int $expires 
	 * @return $this 
	 */
	public function setExpires($expires) {
		$this->expiry = $expires;
		return $this;
	}

	/** @return int|string  */
	public function getExpires(){
		return $this->expiry ?: $this->config->getExpires();
	}

	/**
	 * @param string $path 
	 * @return $this 
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}

	/** @return string  */
	public function getPath(){
		return $this->path ?: $this->config->getPath();
	}

	/**
	 * @param string $domain 
	 * @return $this 
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}

	/** @return string  */
	public function getDomain() {
		return $this->domain ?: $this->config->getDomain();
	}

	/**
	 * @param bool $secure 
	 * @return $this 
	 */
	public function setSecure($secure) {
		$this->secure = $secure;
		return $this;
	}

	/** @return bool|string  */
	public function getSecure() {
		return $this->secure ?: $this->config->getSecure();
	}

	/**
	 * @param bool $httpOnly 
	 * @return $this 
	 */
	public function setHttpOnly($httpOnly) {
		$this->httpOnly = $httpOnly;
		return $this;
	}

	/** @return bool|string  */
	public function getHttpOnly() {
		return $this->httpOnly !== null ? $this->httpOnly :$this->config->getHttpOnly();
	}

	/**
	 * 
	 * @param \Phacil\Framework\Cookies\SameSite $sameSite 
	 * @return $this 
	 */
	public function setSameSite(SameSite $sameSite) {
		$this->sameSite = $sameSite->getValue();
		return $this;
	}

	/** @return string  */
	public function getSameSite(){
		return $this->sameSite ?: $this->config->getSameSite();
	}

	/**
	 * @param string $cookieName 
	 * @return mixed|null 
	 */
	public function getCookieValue($cookieName){
		return \Phacil\Framework\Request::COOKIE($cookieName);
	}

	/**
	 * @param string $cookieName 
	 * @return mixed|null 
	 */
	public function get($cookieName){
		return $this->getCookieValue($cookieName);
	}

	/**
	 * @param string $cookieName 
	 * @param mixed $value 
	 * @return bool 
	 */
	public function set($cookieName, $value) {
		if($this->setCookie($cookieName, $value)){
			\Phacil\Framework\Request::COOKIE($cookieName, $value);
			return true;
		}
		return false;
	}

	public function setKey($key) {
		if(!is_string($key)) throw new \Phacil\Framework\Exception\InvalidArgumentException('Invalid cookie key value');

		$this->cookie_key = $key;
		return $this;
	}

	/**
	 * @param mixed $value 
	 * @return $this 
	 */
	public function setValue($value) {	
		$this->cookie_value = $value;
		return $this;
	}

	/**
	 * @return bool 
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException 
	 */
	public function save() {
		if(!empty($this->cookie_key)) {
			return $this->set($this->cookie_key, $this->cookie_value);
		}

		throw new \Phacil\Framework\Exception\InvalidArgumentException('Cookie key value is required.');
	}

	/** @return array  */
	public function getCookies() {
		return \Phacil\Framework\Request::COOKIE();
	}

	/**
	 * @param string $name 
	 * @return void 
	 */
	public function deleteCookie($name) {
        if ($this->cookieExists($name)) {
            setcookie($name, '', time() - 3600, $this->path, $this->domain, $this->secure, $this->httpOnly);
            unset($_COOKIE[$name]);
        }
    }

	/**
	 * @param string $name 
	 * @return void 
	 */
	public function unsetCookie($name) {
        return $this->deleteCookie($name);
    }
}