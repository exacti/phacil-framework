<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** 
 * The Request class with escaped values.
 * 
 * @package Phacil\Framework
 * @since 1.0.0
 */
final class Request {
    /**
     * @deprecated since version 2.0, to be removed in 3.0.
     * @var array|string
     */
    public $get = array();

    /**
     * @deprecated since version 2.0, to be removed in 3.0.
     * @var array|string
     */
    public $post = array();

    /**
     * @deprecated since version 2.0, to be removed in 3.0.
     * @var array|string
     */
    public $cookie = array();

    /**
     * @deprecated since version 2.0, to be removed in 3.0.
     * @var array|string
     */
    public $files = array();

    /**
     * @deprecated since version 2.0, to be removed in 3.0.
     * @var array|string
     */
    public $server = array();

    /**
     * @deprecated since version 2.0, to be removed in 3.0.
     * @var array|string
     */
    public $request = [];

    /**
     * @deprecated since version 2.0, to be removed in 3.0.
     * @var array|string|false
     */
    public $method;

    /**
     * 
     * @var array|string
     */
    static protected  $_GET = [];

    /**
     * 
     * @var array|string
     */
    static protected  $_POST = [];

    /**
     * 
     * @var array|string
     */
    static protected  $_REQUEST = [];

    /**
     * 
     * @var array|string
     */
    static protected  $_COOKIE = [];

    /**
     * 
     * @var array|string
     */
    static protected  $_FILES = [];

    /**
     * 
     * @var array|string
     */
    static protected  $_SERVER = [];

    /**
     * 
     * @var array|string
     */
    static protected  $_HEADERS = [];

    /**
     * 
     * @var array|string|false
     */
    static protected  $_METHOD;

    /** @return void  */
    public function __construct() {
        self::$_GET = $this->clean($_GET);
        self::$_POST = $this->clean($_POST);
        self::$_REQUEST = $this->clean($_REQUEST);
        self::$_COOKIE = $this->clean($_COOKIE);
        self::$_FILES = $this->clean($_FILES);
        self::$_SERVER = $this->clean($_SERVER);
        self::$_HEADERS = $this->clean(getallheaders());
        self::$_METHOD = (isset(self::$_SERVER['REQUEST_METHOD'])) ? $this->clean(self::$_SERVER['REQUEST_METHOD']) : false;

        $this->get =& self::$_GET;
        $this->post =& self::$_POST;
        $this->request =& self::$_REQUEST;
        $this->cookie =& self::$_COOKIE;
        $this->files =& self::$_FILES;
        $this->server =& self::$_SERVER;
        $this->method =& self::$_METHOD;
    }

    /**
     * @param string|array $data 
     * @return array|string 
     * 
     * @since 1.0.0
     */
    static public function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[self::clean($key)] = self::clean($value);
            }
        } else {
            $data = (gettype($data) == 'string') ? trim(htmlspecialchars($data, ENT_COMPAT)) : $data;
        }

        return $data;
    }

    /**
     * Return POST values from $_POST or $_FILES.
     * 
     * @param string $key (optional)
     * @param mixed $value (optional)
     * @return mixed 
     * 
     * @since 2.0.0
     */
    static public function POST($key = null, $value = null){
        if($value !== null)
            self::$_POST[$key] = $value;

        return $key ? (isset(self::$_POST[$key]) ? self::$_POST[$key] : null) : self::$_POST;
    }

    /**
     * Return php://input from POST or PUT requests.
     * 
     * @param string $key (optional)
     * @return string|array 
     * 
     * @since 2.0.0
     */
    static public function INPUT($key = null){
        if (self::HEADER("Content-Type") == 'application/json'){
            try{
                $data = self::clean(JSON::decode(file_get_contents('php://input')));
            } catch (\Exception $e){
                throw new \UnexpectedValueException($e->getMessage(), $e->getCode());
            }
        } else {
            $data = self::clean(file_get_contents('php://input'));
        }

        return (self::HEADER("Content-Type") == 'application/json') ? (($key) ? (isset($data[$key]) ? $data[$key] : null) : $data) : ($data ?: null);
    }

    /**
     * Return GET values from $_GET.
     * 
     * @param string $key (optional)
     * @param mixed $value (optional)
     * @return mixed 
     * 
     * @since 2.0.0
     */
    static public function GET($key = null, $value = null){
        if ($value !== null)
            self::$_GET[$key] = $value;

        return $key ? (isset(self::$_GET[$key]) ? self::$_GET[$key] : null) : self::$_GET;
    }

    /**
     * Return REQUEST values from $_REQUEST.
     * 
     * @param string $key (optional)
     * @param mixed $value (optional)
     * @return mixed 
     * 
     * @since 2.0.0
     */
    static public function REQUEST($key = null, $value = null){
        if ($value !== null)
            self::$_REQUEST[$key] = $value;
        
        return $key ? (isset(self::$_REQUEST[$key]) ? self::$_REQUEST[$key] : null) : self::$_REQUEST;
    }

    /**
     * Return FILES values from $_FILES.
     * 
     * @param string $key (optional) If null return all values.
     * @param mixed $value (optional) If not null, set the value for this key.
     * @return mixed 
     * 
     * @since 2.0.0
     */
    static public function FILES($key = null, $value = null){
        if ($value !== null)
            self::$_FILES[$key] = $value;
        
        return $key ? (isset(self::$_FILES[$key]) ? self::$_FILES[$key] : null) : self::$_FILES;
    }

    /**
     * Return COOKIE values from $_COOKIE.
     * 
     * @param string $key 
     * @param mixed $value 
     * @return mixed 
     * 
     * @since 2.0.0
     */
    static public function COOKIE($key = null, $value = null){
        if ($value !== null)
            self::$_COOKIE[$key] = $value;
        
        return $key ? (isset(self::$_COOKIE[$key]) ? self::$_COOKIE[$key] : null) : self::$_COOKIE;
    }

    /**
     * Return SERVER values from $_SERVER.
     * 
     * @param string $key 
     * @param mixed $value 
     * @return mixed 
     * 
     * @since 2.0.0
     */
    static public function SERVER($key = null, $value = null){
        if ($value !== null)
            self::$_SERVER[$key] = $value;
        
        return $key ? (isset(self::$_SERVER[$key]) ? self::$_SERVER[$key] : null) : self::$_SERVER;
    }

    /**
     * Return HEADERS values from getallheaders().
     * 
     * @param string $key 
     * @param mixed $value 
     * @return mixed 
     * 
     * @since 2.0.0
     */
    static public function HEADER($key = null, $value = null){
        if ($value !== null)
            self::$_HEADERS[$key] = $value;
        
        return $key ? (isset(self::$_HEADERS[$key]) ? self::$_HEADERS[$key] : null) : self::$_HEADERS;
    }

    /**
     * Return HTTP method from $_SERVER['REQUEST_METHOD'].
     * 
     * @return string|false|null 
     * 
     * @since 2.0.0
     */
    static public function METHOD(){
        return self::$_METHOD;
    }

    /** @return bool
     * @since 1.5.0
     */
    public function isPOST() {
        return $this->is('POST');
    }

    /** @return bool
     * @since 1.5.0
     */
    public function isGET() {
        return $this->is('GET');
    }

    /** @return bool
     * @since 1.5.0
     */
    public function isHEAD() {
        return $this->is('HEAD');
    }

    /**
     * 
     * @return bool 
     * @since 1.5.0
     */
    public function isPUT() {
        return $this->is('PUT');
    }

    /**
     * 
     * @return bool 
     * @since 1.5.0
     */
    public function isDELETE() {
        return $this->is('DELETE');
    }

    /** @return bool
     * @since 1.5.0
     */
    public function isCONNECT() {
        return $this->is('CONNECT') ;
    }

    /** @return bool
     * @since 1.5.0
     */
    public function isOPTIONS() {
        return $this->is('OPTIONS') ;
    }

    /** @return bool 
     * @since 1.5.0
     */
    public function isTRACE() {
        return $this->is('TRACE');
    }

    /** @return bool 
     * @since 1.5.0
     */
    public function isPATCH() {
        return $this->is('PATCH');
    }

    /**
     * @param string $method 
     * @return bool 
     * @since 1.5.0
     */
    public function is($method){
        return (self::$_METHOD == $method);
    }
}
