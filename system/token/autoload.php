<?php

function token($length = 32) {
	if(!isset($length) || intval($length) <= 8 ){
		$length = 32;
	}	
	if (function_exists('random_bytes')) {
		$token = bin2hex(random_bytes($length));
	}
	if (function_exists('mcrypt_create_iv') && phpversion() < '7.1') {
		$token = bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
	}
	if (function_exists('openssl_random_pseudo_bytes')) {
		$token = bin2hex(openssl_random_pseudo_bytes($length));
	}
	return substr($token, -$length, $length);
}
