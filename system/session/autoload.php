<?php

/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Credis; 

/** 
 * The session manipulation class
 * 
 * @since 1.0.0
 * @package Phacil\Framework */
final class Session {
    /**
     * 
     * @var array
     */
    public $data = array();

    public $redis = null;

    /**
     * Name of session
     * 
     * @var string
     */
    public $name;

    private $redisPrefix = "phacil_";

    public $redisKey;

    /** @return void  */
    public function __construct() {
        $this->name = ((defined('SESSION_PREFIX')) ? SESSION_PREFIX : 'SESS').(isset($_SERVER['REMOTE_ADDR']) ? md5($_SERVER['REMOTE_ADDR']) : md5(date("dmY")));

        if (!session_id()) {
            $this->openSession();
        }

        $this->redis();

        if(session_name() === $this->name) {
            $this->data =& $_SESSION;
        }else {
            $this->openSession();
            $this->data =& $_SESSION;
        }

    }

    /** @return void  */
    private function openSession() {

        $this->closeSession();

        ini_set('session.use_cookies', 'On');
        ini_set('session.use_trans_sid', 'Off');
        ini_set('session.cookie_httponly', 1);
        if($this->isSecure())
            ini_set('session.cookie_secure', 1);

        session_set_cookie_params(0, '/');
        //session_id(md5());
        session_name($this->name);
        session_start();

    }

    private function redis(){
        global $engine;

        if(!$engine->config->get('session_redis'))
            return false;
        
        $this->redisExpire = ($engine->config->get('session_redis_expire')) ?: session_cache_expire()*60;
        $this->redisPrefix = ($engine->config->get('session_redis_prefix')) ?: 'phacil_';
        $this->redisKey = $this->redisPrefix.session_name().session_id();
        $this->redis = new Credis();
        $_SESSION = json_decode($this->redis->get($this->redisKey), true);

        return $this->redis;
    }

    /** @return void  */
    private function closeSession() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    /** @return bool  */
    private function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }


    public function __destruct()
    {
        if($this->redis){
            $this->redis->set($this->redisKey, json_encode($_SESSION));
            
            $this->redis->expire($this->redisKey, ($this->redisExpire));
        }
    }
}
