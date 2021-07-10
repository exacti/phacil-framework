<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Request {
    public $get = array();
    public $post = array();
    public $cookie = array();
    public $files = array();
    public $server = array();
    public $method;

    /** @return void  */
    public function __construct() {
        $_GET = $this->clean($_GET);
        $_POST = $this->clean($_POST);
        $_REQUEST = $this->clean($_REQUEST);
        $_COOKIE = $this->clean($_COOKIE);
        $_FILES = $this->clean($_FILES);
        $_SERVER = $this->clean($_SERVER);

        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->method = (isset($this->server['REQUEST_METHOD'])) ? $this->clean($this->server['REQUEST_METHOD']) : false;
    }

    /**
     * @param string|array $data 
     * @return array|string 
     */
    public function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT);
        }

        return $data;
    }

    /** @return bool  */
    public function isPOST() {
        return $this->is('POST');
    }

    /** @return bool  */
    public function isGET() {
        return $this->is('GET');
    }

    /** @return bool  */
    public function isHEAD() {
        return $this->is('HEAD');
    }

    /**
     * 
     * @return bool 
     */
    public function isPUT() {
        return $this->is('PUT');
    }
    
    /**
     * 
     * @return bool 
     */
    public function isDELETE() {
        return $this->is('DELETE');
    }
    
    /** @return bool  */
    public function isCONNECT() {
        return $this->is('CONNECT') ;
    }
    
    /** @return bool  */
    public function isOPTIONS() {
        return $this->is('OPTIONS') ;
    }
    
    /** @return bool  */
    public function isTRACE() {
        return $this->is('TRACE');
    }
    
    /** @return bool  */
    public function isPATCH() {
        return $this->is('PATCH');
    }
    
    /**
     * @param string $method 
     * @return bool 
     */
    public function is($method){
        return ($this->method == $method) ? true : false;
    }
}
