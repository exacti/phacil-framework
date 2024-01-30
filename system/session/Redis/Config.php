<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Session\Redis;

use Cm\RedisSession\Handler\ConfigInterface;

class Config implements ConfigInterface {

	/**
	 * 
	 * @var int
	 */
	private $logLevel = 1;

	const PARAM_HOST = 'tcp://127.0.0.1';

	const PARAM_PORT = 6379;

	const PARAM_DATABASE = 3;

	const DEFAULT_COMPRESSION_THRESHOLD = 2048;

	const PARAM_COMPRESSION_LIBRARY = 'gzip';

	const PARAM_TIMEOUT = 2.5;

	const PARAM_MAX_CONCURRENCY = 12;

	const PARAM_BREAK_AFTER = 30;

	const PARAM_SENTINEL_CONNECT_RETRIES = null;

	/**
	 * Session max lifetime
	 */
	const SESSION_MAX_LIFETIME = 31536000;

	/**
	 * Try to break lock for at most this many seconds
	 */
	const DEFAULT_FAIL_AFTER = 15;

	const PARAM_MIN_LIFETIME = 60;

	const PARAM_DISABLE_LOCKING = true;

	const PARAM_BOT_LIFETIME = 7200;

	const PARAM_BOT_FIRST_LIFETIME = 60;

	const PARAM_FIRST_LIFETIME = 600;

	const PARAM_SESSION_LIFETIME = 31536000;

	const PARAM_SENTINEL_SERVERS = null;

	/**
	 * 
	 * @var \Phacil\Framework\Registry
	 */
	protected $engine;

	/** 
	 * Get the Framework instance
	 * @return void 
	 */
	public function __construct(){
		$this->engine = \Phacil\Framework\Registry::getInstance();
		$this->logLevel = \Phacil\Framework\Config::Debug() ? 6 : $this->logLevel;
		$fg = [];
		foreach (get_class_methods($this) as $key => $value) {
			# code...
			if ($value != '__construct' && $value != 'getLogLevel')
			$fg[$value] = $this->$value();
		}

		return;
	}

	/**
	 * 
	 * {@inheritdoc}
	 */
	function getLogLevel() {
		return $this->engine->config->get('session_redis_log_level') ?: $this->logLevel;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHost()
	{
		return $this->engine->config->get('session_redis_dsn') ? : self::PARAM_HOST;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPort()
	{
		return $this->engine->config->get('session_redis_port') ?: self::PARAM_PORT;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDatabase()
	{
		return (string)$this->engine->config->get('session_redis_database') ?: self::PARAM_DATABASE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPassword()
	{
		return $this->engine->config->get('session_redis_password') ?: '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTimeout()
	{
		return $this->engine->config->get('session_redis_timeout') ? : self::PARAM_TIMEOUT;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPersistentIdentifier()
	{
		return $this->engine->config->get('session_redis_persistent_id') ?: '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCompressionThreshold()
	{
		return $this->engine->config->get('session_redis_compression_threshold') ?: self::DEFAULT_COMPRESSION_THRESHOLD ;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCompressionLibrary()
	{
		return $this->engine->config->get('session_redis_compression_library') ?: (self::PARAM_COMPRESSION_LIBRARY);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMaxConcurrency()
	{
		return $this->engine->config->get('session_redis_max_concurrency') ?: (self::PARAM_MAX_CONCURRENCY);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMaxLifetime()
	{
		return $this->engine->config->get('session_redis_max_lifetime') ?: self::SESSION_MAX_LIFETIME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMinLifetime()
	{
		return (int)$this->engine->config->get('session_redis_min_lifetime') ?: (self::PARAM_MIN_LIFETIME);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDisableLocking()
	{
		return (bool)$this->engine->config->get('session_redis_disable_locking') ?:  (self::PARAM_DISABLE_LOCKING);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBotLifetime()
	{
		return (int) $this->engine->config->get('session_redis_bot_lifetime') ?: (self::PARAM_BOT_LIFETIME);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBotFirstLifetime()
	{
		return (string)$this->engine->config->get('session_redis_bot_first_lifetime') ?: (self::PARAM_BOT_FIRST_LIFETIME);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFirstLifetime()
	{
		return (int)$this->engine->config->get('session_redis_first_lifetime') ?: (self::PARAM_FIRST_LIFETIME);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBreakAfter()
	{
		return (int)$this->engine->config->get('session_redis_break_after') ?: (self::PARAM_BREAK_AFTER);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLifetime()
	{
		return (int)$this->engine->config->get('session_redis_expire') ?: self::PARAM_SESSION_LIFETIME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSentinelServers()
	{
		return $this->engine->config->get('session_redis_sentinel_servers') ?: null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSentinelMaster()
	{
		return $this->engine->config->get('session_redis_sentinel_master') ?: null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSentinelVerifyMaster()
	{
		return $this->engine->config->get('session_redis_verify_master') ?: (null);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSentinelConnectRetries()
	{
		return $this->engine->config->get('session_redis_sentinel_connect_retries') ?: (self::PARAM_SENTINEL_CONNECT_RETRIES);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFailAfter()
	{
		return (int)$this->engine->config->get('session_redis_fail_after') ?: self::DEFAULT_FAIL_AFTER;
	}

}