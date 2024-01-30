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
 * 
 * @since 1.0.0
 * @package Phacil\Framework 
 */
class Session
{
    /**
     * 
     * @var array
     */
    public $data = array();

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
    private $redisPrefix = "sess_";

    /**
     * Redis Key
     * 
     * @var string
     */
    public $redisKey;

    /**
     * 
     * @var \Phacil\Framework\Session\Redis\Handler
     */
    protected $saveHandler;

    protected $redisExpire;

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
    public function __construct($redis = false)
    {
        $this->name = (Config::SESSION_PREFIX() ?: 'SESS') . (isset($_SERVER['REMOTE_ADDR']) ? md5($_SERVER['REMOTE_ADDR']) : md5(date("dmY")));

        //define('SESSION_PREFIX_INTERNAL_REDIS', Config::REDIS_SESSION_PREFIX() ?: 'phacil_');

        $this->redis($redis);

        if (!session_id()) {
            $this->openSession();
        }

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
     * @return bool
     */
    private function redis($redis = false)
    {

        if (!$redis)
            return false;

        $redisConfig = new \Phacil\Framework\Session\Redis\Config();

        $this->saveHandler = new \Phacil\Framework\Session\Redis\Handler($redisConfig, new \Phacil\Framework\Session\Redis\Logger($redisConfig));

        $this->saveHandler->setName($this->name);

        return $this->registerSaveHandler();
    }

    /**
     * Register save handler
     *
     * @return bool
     */
    protected function registerSaveHandler()
    {
        return session_set_save_handler(
            //$this->saveHandler
            [$this->saveHandler, 'open'],
            [$this->saveHandler, 'close'],
            [$this->saveHandler, 'read'],
            [$this->saveHandler, 'write'],
            [$this->saveHandler, 'destroy'],
            [$this->saveHandler, 'gc']
        );
    }

    /**
     * Close sessions
     * 
     * @param bool $force 
     * @return void 
     */
    private function closeSession($force = false)
    {
        //return ;
        if (session_status() == PHP_SESSION_ACTIVE || $force) {
            session_unset();
            session_destroy();
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
     * Flush all session data
     * @return void 
     * @since 2.0.0
     */
    public function flushAll()
    {
        $this->flush();
    }

    /**
     * Flush current session data
     * @return void 
     * @since 2.0.0
     */
    public function flush()
    {
        $this->data = [];
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
