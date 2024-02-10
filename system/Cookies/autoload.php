<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Cookies\SameSite;

/**
 * @since 2.0.0
 * @package Phacil\Framework
 */
class Cookies {

	/**
	 * 
	 * @var int
	 */
	private $expiry = 0;

	/**
	 * 
	 * @var string
	 */
	private $path = '/';

	/**
	 * 
	 * @var string
	 */
	private $domain = '';

	/**
	 * 
	 * @var bool
	 */
	private $secure = true;

	/**
	 * 
	 * @var bool
	 */
	private $httpOnly = true;

	private $sameSite = SameSite::STRICT;

	/**
	 * 
	 * @param int $expiry 
	 * @param string $path 
	 * @param string $domain 
	 * @param bool $secure 
	 * @param bool $httpOnly 
	 * @param string $sameSite 
	 * @return void 
	 */
	public function __construct($expiry = 0, $path = '/', $domain = "", $secure = true, $httpOnly = true, $sameSite = SameSite::STRICT){
		$this->expiry = $expiry;
		$this->path = $path;
		$this->domain = $domain;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
		$this->sameSite = $sameSite;
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
				return setrawcookie($name, $value, $this->expiry, $this->path . "; samesite=".$this->sameSite, $this->domain, $this->secure, $this->httpOnly);
			}
			return setcookie($name, $value, $this->expiry, $this->path. "; samesite=".$this->sameSite, $this->domain, $this->secure, $this->httpOnly);
		} else {
			if ($isRaw) {
				return setrawcookie($name, $value, [
					'expires' 	=> $this->expiry, 
					'path'		=> $this->path,
					'domain' 	=> $this->domain, 
					'secure' 	=> $this->secure,
					'httponly' 	=> $this->httpOnly,
					'samesite'	=> $this->sameSite
				]);
			}
			return setcookie($name, $value, [
				'expires' 	=> $this->expiry,
				'path'		=> $this->path,
				'domain' 	=> $this->domain,
				'secure' 	=> $this->secure,
				'httponly' 	=> $this->httpOnly,
				'samesite'	=> $this->sameSite
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

	/**
	 * @param string $path 
	 * @return $this 
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}

	/**
	 * @param string $domain 
	 * @return $this 
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}

	/**
	 * @param bool $secure 
	 * @return $this 
	 */
	public function setSecure($secure) {
		$this->secure = $secure;
		return $this;
	}

	/**
	 * @param bool $httpOnly 
	 * @return $this 
	 */
	public function setHttpOnly($httpOnly) {
		$this->httpOnly = $httpOnly;
		return $this;
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