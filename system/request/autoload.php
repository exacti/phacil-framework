<?php
final class Request {
    public $get = array();
    public $post = array();
    public $cookie = array();
    public $files = array();
    public $server = array();
    public $method;

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

    public function isPOST() {
        return $this->is('POST');
    }
    public function isGET() {
        return $this->is('GET');
    }
    public function isHEAD() {
        return $this->is('HEAD');
    }
    public function isPUT() {
        return $this->is('PUT');
    }
    public function isDELETE() {
        return $this->is('DELETE');
    }
    public function isCONNECT() {
        return $this->is('CONNECT') ;
    }
    public function isOPTIONS() {
        return $this->is('OPTIONS') ;
    }
    public function isTRACE() {
        return $this->is('TRACE');
    }
    public function isPATCH() {
        return $this->is('PATCH');
    }
    public function is($method){
        return ($this->method == $method) ? true : false;
    }
}
