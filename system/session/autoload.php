<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Config as ConfigFramework;
use Phacil\Framework\Cookies\Config as CookieConfig;

/** 
 * The session manipulation class
 * 
 * You can activate the Redis session instead use the default PHP session manipulation.
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
    private $saveHandler;

    /**
     * 
     * @var \Phacil\Framework\Registry
     */
    private $registry;

    /**
     * 
     * @var \Phacil\Framework\Cookies\Config
     */
    private $cookieConfig;

    /**
     * 
     * @param \Phacil\Framework\Registry $registry 
     * @param \Phacil\Framework\Config $config 
     * @param \Phacil\Framework\Cookies\Config $cookieConfig 
     * @return void 
     * @throws \Phacil\Framework\Exception 
     */
    public function __construct(
        \Phacil\Framework\Registry $registry,
        ConfigFramework $config,
        CookieConfig $cookieConfig
    ) {
        $this->registry = $registry;

        $this->cookieConfig = $cookieConfig;

        $this->name = (Config::SESSION_PREFIX() ?: 'SESS') . (isset($_SERVER['REMOTE_ADDR']) ? md5($_SERVER['REMOTE_ADDR']) : md5(date("dmY")));

        //define('SESSION_PREFIX_INTERNAL_REDIS', Config::REDIS_SESSION_PREFIX() ?: 'phacil_');

        $this->redis($config->get('session_redis'));

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
        ini_set('session.cookie_httponly', $this->cookieConfig->getHttpOnly());
        ini_set('session.cookie_secure', $this->cookieConfig->getSecure());

        if (version_compare(phpversion(), "7.3.0", "<")) {
            session_set_cookie_params($this->cookieConfig->getExpires(), $this->cookieConfig->getPath().'; samesite=' . $this->cookieConfig->getSameSite(), $this->cookieConfig->getDomain(), $this->cookieConfig->getSecure(), $this->cookieConfig->getHttpOnly());
        } else {
            session_set_cookie_params([
                'lifetime' => $this->cookieConfig->getExpires(),
                'path' => $this->cookieConfig->getPath(),
                'samesite' => $this->cookieConfig->getSameSite(),
                'domain' => $this->cookieConfig->getDomain(),
                'secure' => $this->cookieConfig->getSecure(),
                'httponly' => $this->cookieConfig->getHttpOnly(),
            ]);
        }
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

        if (!\Phacil\Framework\Registry::checkPreferenceExist(\Phacil\Framework\Session\Api\HandlerInterface::class)) {
            \Phacil\Framework\Registry::addDIPreference(\Phacil\Framework\Session\Api\HandlerInterface::class, \Phacil\Framework\Session\Handlers\Redis::class);
        }

        $this->saveHandler = $this->registry->getInstance(\Phacil\Framework\Session\Api\HandlerInterface::class);

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
