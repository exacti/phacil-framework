<?php
final class Caches {
    private $expire = 3600;

    public $dirCache = DIR_CACHE."caches/";

    public function __construct() {
        if (!file_exists($this->dirCache)) {
            mkdir($this->dirCache, 0755, true);
        }
        $this->expire = (defined('CACHE_EXPIRE')) ? CACHE_EXPIRE : 3600;
        /*$files = glob($this->dirCache . '*.cache');

        if ($files) {
            foreach ($files as $file) {
                //$time = substr(strrchr($file, '.'), 1);
                $time = substr(strrchr(strstr($file, '.', true), '/'), 1);
                //var_dump(substr(strrchr(strstr($file, '.', true), '/'), 1));

                  if ($time < time() and $time !== '0') {
                    if (file_exists($file)) {
                        unlink($file);
                        clearstatcache();
                    }
                  }
            }
        }*/
    }

    public function verify($key) {
        $files = $this->valid($key);

        if ($files) {

            return true;

        } else {

            return false;
        }
    }

    public function check($key) {
        return $this->verify($key);
    }

    public function get($key) {

        $file = $this->valid($key);

        if ($file) {
            $cache = file_get_contents($file);

            return $this->decode($cache);
        }
    }

    public function set($key, $value, $expire = true) {
        $this->delete($key);

        //$exp = ($expire == true) ? (time() + $this->expire) : 0;

        $file = $this->dirCache  . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache';

        return file_put_contents($file, $this->encode($value));

    }

    public function delete($key) {
        $files = glob($this->dirCache . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');

        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                    clearstatcache();
                }
            }
        }
    }

    private function encode($value){

        if(function_exists('igbinary_serialize')){
            return igbinary_serialize($value);
        } else {
            return serialize($value);
        }

    }

    private function decode($value){

        if(function_exists('igbinary_serialize')){
            return igbinary_unserialize($value);
        } else {
            return unserialize($value);
        }
    }

    private function valid($key) {
        $file = ($this->dirCache . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');

        if (file_exists($file)) {
            //var_dump($file);

            //$time = substr(strrchr(strstr($files[0], '.', true), '/'), 1);
            $time = filemtime($file) + $this->expire;
            //var_dump(substr(strrchr(strstr($file, '.', true), '/'), 1));


            if ($time < time() and $time !== '0') {

                //var_dump($file);
                unlink($file);
                clearstatcache();
                return false;

            } else {

                return $file;

            }
        } else {
            return false;
        }
    }

    public function deleteAll() {
        $files = glob($this->dirCache . '*.cache');

        if ($files) {
            foreach ($files as $file) {
                //$time = substr(strrchr($file, '.'), 1);
                //$time = substr(strrchr(strstr($file, '.', true), '/'), 1);
                //var_dump(substr(strrchr(strstr($file, '.', true), '/'), 1));

                if (file_exists($file)) {
                    unlink($file);
                    clearstatcache();
                }
            }
        }

        return true;
    }

    public function clear() {
        return $this->deleteAll();
    }

    public function stats() {

        $obj = new stdClass();

        $obj->size = $this->GetDirectorySize($this->dirCache);
        $obj->info = NULL;
        $obj->rawData = NULL;
        $obj->data = NULL;

        return $obj;
    }

    private function GetDirectorySize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;

    }
}