<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Credis;
use Phacil\Framework\Config;

/** 
 * The session manipulation class
 * 
 * You can activate the Redis session instead use the default PHP session manipulation.
 * 
 * @param bool $redis Active or not the Redis session
 * @param string|null $redisDSN (optional) Redis DSN like unix:///path/to/redis.sock, tcp://host[:port][/persistence_identifier] or tls://host[:port][/persistence_identifier]. If not specified, the default value is connect to localhost in 6379 port.
 * @param int|null $redisPort (optional) If is not in the DSN specification, we can pass as separated value. The default is 6379.
 * @param string|null $redisPass (optional) Redis conection password
 * @param int|null $redis_expire (optional) Redis session expiration, the defaul is your session_cache_expire configurated in PHP *60.
 * @param string $redis_prefix (optional) The default prefis is 'phacil_'.
 * 
 * @since 1.0.0
 * @package Phacil\Framework 
 */
final class Session
{
    /**
     * 
     * @var array
     */
    public $data = array();

    /**
     * Redis object
     * @var Credis
     */
    public $redis = null;

    /**
     * Name of session
     * 
     * @var string
     */
    public $name;

    /**
     * Redis prefix
     * 
     * @var string
     */
    private $redisPrefix = "phacil_";

    /**
     * Redis Key
     * 
     * @var string
     */
    public $redisKey;

    /**
     * 
     * @param bool $redis 
     * @param string|null $redisDSN 
     * @param int|null $redisPort 
     * @param string|null $redisPass 
     * @param int|null $redis_expire 
     * @param string $redis_prefix 
     * @return void 
     */
    public function __construct($redis = false, $redisDSN = null, $redisPort = null, $redisPass = null, $redis_expire = null, $redis_prefix = 'phacil_')
    {
        $this->name = (Config::SESSION_PREFIX() ?: 'SESS') . (isset($_SERVER['REMOTE_ADDR']) ? md5($_SERVER['REMOTE_ADDR']) : md5(date("dmY")));

        if (!session_id()) {
            $this->openSession();
        }

        $this->redis($redis, $redisDSN, $redisPort, $redisPass, $redis_expire, $redis_prefix);

        if (session_name() === $this->name) {
            $this->data =& $_SESSION;
        } else {
            $this->openSession();
            $this->data =& $_SESSION;
        }
    }

    /** 
     * Open the PHP session
     * 
     * @return void  
     */
    private function openSession()
    {

        $this->closeSession();

        ini_set('session.use_cookies', 'On');
        ini_set('session.use_trans_sid', 'Off');
        ini_set('session.cookie_httponly', 1);
        if ($this->isSecure())
            ini_set('session.cookie_secure', 1);

        session_set_cookie_params(0, '/');
        //session_id(md5());
        session_name($this->name);
        session_start();
    }

    /**
     * Check and iniciate the Redis connection
     * 
     * @param bool $redis 
     * @param string|null $redisDSN 
     * @param string|null $redisPort 
     * @param string|null $redisPass 
     * @param int|null $redis_expire 
     * @param string $redis_prefix 
     * 
     * @since 2.0.0
     * @return false|Credis 
     */
    private function redis($redis = false, $redisDSN = null, $redisPort = null, $redisPass = null, $redis_expire = null, $redis_prefix = 'phacil_')
    {

        if (!$redis)
            return false;

        $this->redisExpire = ($redis_expire) ?: session_cache_expire() * 60;
        $this->redisPrefix = ($redis_prefix) ?: 'phacil_';
        $this->generateRedisKey();

        /**
         * Instanciate the Credis object
         * 
         * @var \Phacil\Framework\Credis
         */
        $this->redis = new Credis((($redisDSN) ?: '127.0.0.1'), (($redisPort) ?: '6379'), (($redisPass) ?: null));

        $_SESSION = unserialize($this->redis->get($this->redisKey));

        return $this->redis;
    }

    /** 
     * Generate the Redis Session KEY
     * 
     * @return void  
     */
    private function generateRedisKey()
    {
        if (session_id())
            $this->redisKey = $this->redisPrefix . session_name() . session_id();

        return $this->redisKey;
    }

    /**
     * Close sessions
     * 
     * @param bool $force 
     * @return void 
     */
    private function closeSession($force = false)
    {
        if (session_status() == PHP_SESSION_ACTIVE || $force) {
            session_unset();
            session_destroy();
        }
        if ($this->redis && $force) {
            $this->redis->close();
            unset($this->redis);
        }
    }

    /** 
     * Check if is secure (SSL) connection
     * @return bool  
     */
    private function isSecure()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }

    /**
     * Set the Redis session data
     * @return void 
     * @since 2.0.0
     */
    public function __destruct()
    {
        if ($this->redis) {
            $this->generateRedisKey();

            $this->redis->set($this->redisKey, serialize($_SESSION));

            $this->redis->expire($this->redisKey, ($this->redisExpire));
        }
    }

    /**
     * Flush all session data
     * @return void 
     * @since 2.0.0
     */
    public function flushAll()
    {
        $this->data = [];
        if ($this->redis) {
            ($this->redis->flushAll());
        }
        $this->closeSession(true);
    }

    /**
     * Flush current session data
     * @return void 
     * @since 2.0.0
     */
    public function flush()
    {
        $this->data = [];
        if ($this->redis) {
            ($this->redis->del($this->generateRedisKey()));
        }
        $this->closeSession(true);
    }

    /**
     * Return the current session ID
     * 
     * @since 2.0.0
     * @return string|false 
     */
    public function getSessionId()
    {
        return session_id();
    }

    /**
     * 
     * @param string $key 
     * @return mixed|null 
     */
    public function getData($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * 
     * @param string $key 
     * @param mixed $value 
     * @return $this 
     */
    public function setData($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
}
