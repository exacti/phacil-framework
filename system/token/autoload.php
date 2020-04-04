<?php

function token($length = 32) {
    if(!isset($length) || intval($length) <= 8 ){
        $length = 32;
    }

    if (function_exists('openssl_random_pseudo_bytes')) {
        $token = bin2hex(openssl_random_pseudo_bytes($length));
    } elseif (function_exists('random_bytes')) {
        $token = bin2hex(random_bytes($length));
    } elseif (function_exists('mcrypt_create_iv') && phpversion() < '7.1') {
        $token = bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
    } else {
        $token = md5(rand(0, 4000).time());
    }

    return substr($token, -$length, $length);
}


if (!function_exists('hash_equals')) {
    function hash_equals($known_string, $user_string) {
        $known_string = (string)$known_string;
        $user_string = (string)$user_string;

        if (strlen($known_string) != strlen($user_string)) {
            return false;
        } else {
            $res = $known_string ^ $user_string;
            $ret = 0;

            for ($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);

            return !$ret;
        }
    }
}