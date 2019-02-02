<?php
final class Encryption {
	private $key;
	private $method;
	private $cipher;
	
	function __construct($key, $opensslCipher = 'aes-128-cbc') {
		$this->key = $this->hash($key);

		if(function_exists('openssl_encrypt')) {
			$this->method = 'openssl';
			$this->cipher = $opensslCipher;
		} else {
			$this->method = 'base64';
		}

	}

	public function hash($value) {
		return hash('sha256', $value, true);
	}

	public function encrypt ($value, $key = NULL) {
		$this->key = ($key != NULL) ? $key : $this->key;

		if($this->method == 'openssl') {
			return $this->opensslEncrypt($value);
		} else {
			return $this->base64Encrypt($value);
		}
	}

	public function decrypt ($value, $key = NULL) {
		$this->key = ($key != NULL) ? $key : $this->key;

		if($this->method == 'openssl') {
			return $this->opensslDecrypt($value);
		} else {
			return $this->base64Decrypt($value);
		}
	}
	
	function base64Encrypt($value) {
		if (!$this->key) { 
			return $value;
		}
		
		$output = '';
		
		for ($i = 0; $i < strlen($value); $i++) {
			$char = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			
			$output .= $char;
		} 
		
		return base64_encode($output);
	}
	
	function base64Decrypt($value) {
		if (!$this->key) { 
			return $value;
		}
		
		$output = '';
		
		$value = base64_decode($value);
		
		for ($i = 0; $i < strlen($value); $i++) {
			$char = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			
			$output .= $char;
		}
		
		return $output;
	}

	private function opensslEncrypt ($value) {

		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));

		$ciphertext_raw = strtr(base64_encode(openssl_encrypt($value, $this->cipher, $this->hash( $this->key), 0, $iv)), '+/=', '-_,');

		//$hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, true);

		$output = $this->base64Encrypt( $iv.$ciphertext_raw );

		return $output;

	}

	private function opensslDecrypt ($value) {

		$c = $this->base64Decrypt($value);
		$ivlen = openssl_cipher_iv_length($this->cipher);
		$iv = substr($c, 0, $ivlen);
		//$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen);


		$output = trim(openssl_decrypt(base64_decode(strtr($ciphertext_raw, '-_,', '+/=')), $this->cipher, $this->hash($this->key), 0, $iv));

		return $output;
	}
}
?>