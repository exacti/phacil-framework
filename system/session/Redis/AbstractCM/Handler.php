<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Session\Redis\AbstractCM;

//use Phacil\Framework\Config;

class Handler extends \Cm\RedisSession\Handler
{
	const SESSION_PREFIX = parent::SESSION_PREFIX;

	public $_name = null;
	/**
	 * Write session data to Redis
	 *
	 * @param $id
	 * @param $data
	 * @param $lifetime
	 * @throws \Exception
	 */
	protected function _writeRawSession($id, $data, $lifetime)
	{
		if (version_compare(phpversion(), '7.4', '>=')) {
			$sessionId = self::SESSION_PREFIX . $id;
			$this->_redis->pipeline()
				->select($this->_dbNum)
				->hMSet($sessionId, array(
					'data' => $this->_encodeData($data),
					'lock' => 0, // 0 so that next lock attempt will get 1
				))
				->hIncrBy($sessionId, 'writes', 1)
				->expire($sessionId, min((int)$lifetime, (int)$this->_maxLifetime))
				->exec();
		} else {
			$sessionId =  self::SESSION_PREFIX . $id;
			$redis =  $this->_redis;
			$redis->select($this->_dbNum);
			$redis->hMSet($sessionId, array(
				'data' => $this->_encodeData(serialize($data)),
				'lock' => 0, // 0 so that next lock attempt will get 1
			));
			$redis->hIncrBy($sessionId, 'writes', 1);

			$redis->expire($sessionId, min((int)$lifetime, (int)$this->_maxLifetime));
		//->exec();
		//$redis->exec();
		}
		
	}

	public function setName($name)
	{
		$this->_name = $name;
	}
}
