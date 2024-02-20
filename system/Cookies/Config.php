<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Cookies;

use Phacil\Framework\Config as FrameworkConfig;

/**
 * @since 2.0.0
 * @package Phacil\Framework\Cookies
 */
class Config
{
	const STRICT = 'Strict';

	const LAX = 'Lax';

	const NONE = 'None';

	const DEFAULT_HTTP_ONLY = true;

	const DEFAULT_DOMAIN = '';

	const DEFAULT_PATH = '/';

	const DEFAULT_EXPIRY = 0;

	/**
	 * 
	 * @var string[]
	 */
	private $value = [];

	public function __construct(
		FrameworkConfig $config
	) {
		//$this->config = $config;
		$this->value['sameSite'] = $config->get('cookie_same_site') ?: self::STRICT;
		$this->value['expires'] = $config->get('cookie_expires') ?: self::DEFAULT_EXPIRY;
		$this->value['path'] = $config->get('cookie_path') ?: self::DEFAULT_PATH;
		$this->value['domain'] = $config->get('cookie_domain') ?: self::DEFAULT_DOMAIN;
		$this->value['secure'] = $config->get('cookie_secure') !== null ? $config->get('cookie_secure') : ($this->isSecure());
		$this->value['httpOnly'] = $config->get('cookie_http_only') !== null ? $config->get('cookie_http_only') : self::DEFAULT_HTTP_ONLY;
	}

	/** @return $this  */
	public function setStrict()
	{
		$this->value['sameSite'] = self::STRICT;
		return $this;
	}

	/** @return $this  */
	public function setLax()
	{
		$this->value['sameSite'] = self::LAX;
		return $this;
	}

	/** @return $this  */
	public function setNone()
	{
		$this->value['sameSite'] = self::NONE;
		return $this;
	}

	/**
	 * @param string $key 
	 * @return string 
	 */
	public function getValue($key)
	{
		return $this->value[$key];
	}

	/** @return string[]  */
	public function getValues()
	{
		return $this->value;
	}

	/**
	 * @param string $key 
	 * @param string|int $value 
	 * @return $this 
	 */
	public function setValue($key, $value) {
		$this->value[$key] = $value;
		return $this;
	}

	/** 
	 * Check if is secure (SSL) connection
	 * @return bool  
	 */
	public function isSecure()
	{
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}

	/** @return string  */
	public function getSameSite() {
		return $this->value['sameSite'];
	}

	/** @return string  */
	public function getExpires() {
		return $this->value['expires'];
	}

	/** @return string  */
	public function getPath() {
		return $this->value['path'];
	}

	/** @return string  */
	public function getDomain() {
		return $this->value['domain'];
	}

	/** @return string  */
	public function getSecure() {
		return $this->value['secure'];
	}

	/** @return string  */
	public function getHttpOnly() {
		return $this->value['httpOnly'];
	}

	/**
	 * 
	 * @return $this
	 */
	static public function getInstance()
	{
		$class = get_called_class();
		return \Phacil\Framework\Registry::getAutoInstance((new $class()));
	}
}
