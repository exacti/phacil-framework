<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

final class Caches {
    /**
     * 
     * @var int
     */
    private $expire = 3600;

    /**
     * 
     * @var string
     */
    public $dirCache = "caches/";

    /** @return void  */
    public function __construct() {
        $this->dirCache = \Phacil\Framework\Config::DIR_CACHE()."caches/";

        if (!file_exists($this->dirCache)) {
            mkdir($this->dirCache, 0760, true);
        }
        $this->expire = \Phacil\Framework\Config::CACHE_EXPIRE() ?: 3600;

    }

    /**
     * @param string $key 
     * @return bool 
     */
    public function verify($key) {
        $files = $this->valid($key);

        return ($files) ? true : false;
    }

    /**
     * @param string $key 
     * @return bool 
     */
    public function check($key) {
        return $this->verify($key);
    }

    /**
     * @param string $key 
     * @return mixed 
     */
    public function get($key) {

        $file = $this->valid($key);

        if ($file) {
            $cache = file_get_contents($file);

            return $this->decode($cache);
        }
    }

    /**
     * @param string $key 
     * @param mixed $value 
     * @param bool $expire 
     * @return int|false 
     */
    public function set($key, $value, $expire = true) {
        $this->delete($key);

        $file = $this->dirCache  . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache';

        return file_put_contents($file, $this->encode($value));

    }

    /**
     * @param string $key 
     * @return int 
     */
    public function delete($key) {
        $files = glob($this->dirCache . preg_replace('/[^A-Z0-9\.\*_-]/i', '', $key) . '.cache');

        if ($files) {
            foreach ($files as $file) {
                
                if (file_exists($file)) {
                    
                    unlink($file);
                    
                }
            }
        }

        return (count($files));
    }

    /**
     * @param mixed $value 
     * @return string|binary  
     */
    private function encode($value){

        if(function_exists('igbinary_serialize')){
            return igbinary_serialize($value);
        } else {
            return serialize($value);
        }

    }

    /**
     * @param string $value 
     * @return string 
     */
    private function decode($value){

        if(function_exists('igbinary_serialize')){
            return igbinary_unserialize($value);
        } else {
            return unserialize($value);
        }
    }

    /**
     * @param string $key 
     * @return false|string 
     */
    private function valid($key) {
        $file = ($this->dirCache . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');

        if (file_exists($file)) {

            $time = filemtime($file) + $this->expire;


            if ($time < time() and $time !== '0') {

                unlink($file);

                return false;

            } else {

                return $file;

            }
        } else {
            return false;
        }
    }

    /** @return true  */
    public function deleteAll() {
        $files = glob($this->dirCache . '*.cache');

        array_map('unlink', $files);

        unset($files);

        return true;
    }

    /** @return bool  */
    public function clear() {
        return $this->deleteAll();
    }

    /** @return Phacil\Framework\stdClass  */
    public function stats() {

        $obj = new \stdClass();

        $obj->size = $this->GetDirectorySize($this->dirCache);
        $obj->info = NULL;
        $obj->rawData = NULL;
        $obj->data = NULL;

        return $obj;
    }

    /**
     * @param string $path 
     * @return float|int 
     */
    private function GetDirectorySize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;

    }
}