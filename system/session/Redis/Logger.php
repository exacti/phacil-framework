<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */


namespace Phacil\Framework\Session\Redis;

use Phacil\Framework\Exception;

class Logger implements \Cm\RedisSession\Handler\LoggerInterface
{
	/**
	 * @var int
	 */
	private $logLevel;

	/**
	 * Logger constructor
	 *
	 * @param \Cm\RedisSession\Handler\ConfigInterface $config
	 */
	public function __construct(\Cm\RedisSession\Handler\ConfigInterface $config)
	{
		$this->logLevel = $config->getLogLevel() ?: self::ALERT;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLogLevel($level)
	{
		$this->logLevel = $level;
	}

	/**
	 * {@inheritdoc}
	 */
	public function log($message, $level)
	{
		$engine = \Phacil\Framework\Registry::getInstance();
		if ($this->logLevel >= $level) {
			switch ($level) {
				case self::EMERGENCY:
				case self::CRITICAL:
				case self::ERROR:
					trigger_error($message, E_ERROR);
					break;
				case self::ALERT:
				case self::WARNING:
					//$this->logger->alert($message);
					$engine->log->write($message,E_WARNING);
					break;
				case self::NOTICE:
				case self::INFO:
					$engine->log->write($message);
					break;
				default:
					$engine->log->write($message);
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function logException(\Exception $e)
	{
		throw new Exception($e->getMessage());
	}
}
