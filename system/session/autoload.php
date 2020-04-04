<?php
final class Session {
    public $data = array();
    private $name;

    public function __construct() {
        $this->name = ((defined('SESSION_PREFIX')) ? SESSION_PREFIX : 'SESS').(isset($_SERVER['REMOTE_ADDR']) ? md5($_SERVER['REMOTE_ADDR']) : md5(date("dmY")));

        if (!session_id()) {
            $this->openSession();
        }

        if(session_name() === $this->name) {
            $this->data =& $_SESSION;
        }else {
            $this->openSession();
            $this->data =& $_SESSION;
        }

    }

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

    private function closeSession() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    private function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }
}
