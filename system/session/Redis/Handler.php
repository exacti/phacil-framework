<?php 
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Session\Redis;

use Cm\RedisSession\Handler\ConfigInterface;
use Cm\RedisSession\Handler\LoggerInterface;
use Cm\RedisSession\ConnectionFailedException;
use Cm\RedisSession\ConcurrentConnectionsExceededException;
use Phacil\Framework\Exception;

class Handler implements \SessionHandlerInterface {


	private $savePath;

	/**
	 * 
	 * @var \Cm\RedisSession\Handler[]
	 */
	private $connection;

	/**
	 * 
	 * @var \Cm\RedisSession\Handler\ConfigInterface
	 */
	private $config;

	/**
	 * 
	 * @var \Cm\RedisSession\Handler\LoggerInterface
	 */
	private $logger;

	private $name;

	/**
	 * @param ConfigInterface $config
	 * @param LoggerInterface $logger
	 * @param Filesystem $filesystem
	 * @throws Exception
	 */
	public function __construct(ConfigInterface $config, LoggerInterface $logger)
	{
		$this->config = $config;
		$this->logger = $logger;
	}


	/**
	 * Get connection
	 *
	 * @return \Cm\RedisSession\Handler
	 * @throws Exception
	 */
	private function getConnection()
	{
		$pid = getmypid();
		if (!isset($this->connection[$pid])) {
			try {
				//$this->connection[$pid] = new \Cm\RedisSession\Handler($this->config, $this->logger);
				$this->connection[$pid] = new \Phacil\Framework\Session\Redis\AbstractCM\Handler($this->config, $this->logger);
			} catch (ConnectionFailedException $e) {
				throw new Exception(($e->getMessage()));
			}
		}
		return $this->connection[$pid];
	}

	function setName($name){
		$this->name = $name;
	}

	/**
	 * Open session
	 *
	 * @param string $savePath ignored
	 * @param string $sessionName ignored
	 * @return bool
	 * @throws Exception
	 */
	#[\ReturnTypeWillChange]
	public function open($savePath, $sessionName)
	{
		return (bool)$this->getConnection()->open($savePath, $sessionName);
	}

	/**
	 * Fetch session data
	 *
	 * @param string $sessionId
	 * @return string
	 * @throws ConcurrentConnectionsExceededException
	 * @throws Exception
	 */
	#[\ReturnTypeWillChange]
	public function read($sessionId)
	{
		try {
			return $this->getConnection()->read($sessionId);
		} catch (ConcurrentConnectionsExceededException $e) {
			throw new Exception($e->getMessage(), 1);
			
		}
	}

	/**
	 * Update session
	 *
	 * @param string $sessionId
	 * @param string $sessionData
	 * @return boolean
	 * @throws Exception
	 */
	#[\ReturnTypeWillChange]
	public function write($sessionId, $sessionData)
	{
		try {
			//$this->getConnection()->setName($this->name);
			$cha = $this->getConnection()->write($sessionId, $sessionData);
			return (bool) $cha;
		} catch (ConcurrentConnectionsExceededException $e) {
			throw new Exception($e->getMessage(), 1);
		}
		
	}

	/**
	 * Destroy session
	 *
	 * @param string $sessionId
	 * @return boolean
	 * @throws Exception
	 */
	#[\ReturnTypeWillChange]
	public function destroy($sessionId)
	{
		return (bool) $this->getConnection()->destroy($sessionId);
	}

	/**
	 * Overridden to prevent calling getLifeTime at shutdown
	 *
	 * @return bool
	 * @throws Exception
	 */
	#[\ReturnTypeWillChange]
	public function close()
	{
		return (bool)$this->getConnection()->close();
	}

	/**
	 * Garbage collection
	 *
	 * @param int $maxLifeTime ignored
	 * @return boolean
	 * @throws Exception
	 * @SuppressWarnings(PHPMD.ShortMethodName)
	 */
	#[\ReturnTypeWillChange]
	public function gc($maxLifeTime)
	{
		return $this->getConnection()->gc($maxLifeTime);
	}

	/**
	 * Get the number of failed lock attempts
	 *
	 * @return int
	 * @throws Exception
	 */
	public function getFailedLockAttempts()
	{
		return $this->getConnection()->getFailedLockAttempts();
	}
}