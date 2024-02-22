<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** 
 * Encryption class for Phacil Framework
 * @since 1.0.0
 * @api
 * @package Phacil\Framework 
 */
class Encryption {
    private $key;
    private $method;
    private $cipher;

    private $hash_algo = 'sha256';

    /**
     * @param string $key 
     * @param string $opensslCipher 
     * @return void 
     */
    function __construct($key = null, $opensslCipher = 'aes-128-cbc') {
        if($key) $this->key = $this->hash($key);

        if(function_exists('openssl_encrypt')) {
            $this->method = 'openssl';
            $this->cipher = $opensslCipher;
        } else {
            $this->method = 'base64';
        }

    }

    public function setKey($key) {
        $this->key = $this->hash($key);
    }

    public function getKey() {
        return $this->key;
    }

    /**
     * @param string $hashAlgorithm 
     * @return $this 
     * @throws \Phacil\Framework\Exception\InvalidArgumentException 
     */
    public function setHashAlgorithm($hashAlgorithm) {
        if (!in_array($hashAlgorithm, hash_algos())) {
            throw new \Phacil\Framework\Exception\InvalidArgumentException("The hash algorithm '${hashAlgorithm}' is not available.");
        } 
        $this->hash_algo = $hashAlgorithm;
        return $this;
    }

    /**
     * @param string $hashAlgorithm 
     * @return $this 
     * @throws \Phacil\Framework\Exception\InvalidArgumentException 
     */
    public function setHashAlgo($hashAlgorithm){
        return $this->setHashAlgorithm($hashAlgorithm);
    }

    /** @return string  */
    public function getHashAlgorithm() {
        return $this->hash_algo;
    }

    /** @return string  */
    public function getHashAlgo(){
        return $this->getHashAlgorithm();
    }

    /**
     * @param string $value 
     * @return string|false 
     */
    public function hash($value) {
        //return hash_hmac()
        return hash($this->getHashAlgorithm(), $value);
    }

    /**
     * 
     * @param mixed $value 
     * @param mixed|null $key 
     * @return string 
     */
    public function encrypt ($value, $key = NULL) {
        $this->key = ($key != NULL) ? $key : $this->key;

        if($this->method == 'openssl') {
            return $this->opensslEncrypt($value);
        } else {
            return $this->base64Encrypt($value);
        }
    }

    /**
     * 
     * @param mixed $value 
     * @param mixed|null $key 
     * @return string|false 
     */
    public function decrypt ($value, $key = NULL) {
        $this->key = ($key != NULL) ? $key : $this->key;

        if($this->method == 'openssl') {
            return $this->opensslDecrypt($value);
        } else {
            return $this->base64Decrypt($value);
        }
    }

    /**
     * @param string $value 
     * @return string 
     */
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

    /**
     * @param string $value 
     * @return string|false 
     */
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

    /**
     * @param string $value 
     * @return string 
     */
    private function opensslEncrypt ($value) {

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));

        $ciphertext_raw = strtr(base64_encode(openssl_encrypt($value, $this->cipher, $this->hash( $this->key), 0, $iv)), '+/=', '-_,');

        //$hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, true);

        $output = strtr($this->base64Encrypt( $iv.$ciphertext_raw ), '+/=', '-_,');

        return $output;

    }

    /**
     * @param string $value 
     * @return string|false 
     */
    private function opensslDecrypt ($value) {

        $c = $this->base64Decrypt(strtr($value, '-_,', '+/='));
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($c, 0, $ivlen);
        //$hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen);


        $output = trim(openssl_decrypt(base64_decode(strtr($ciphertext_raw, '-_,', '+/=')), $this->cipher, $this->hash($this->key), 0, $iv));

        return $output;
    }
}
