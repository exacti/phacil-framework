<?php
final class Caches { 
	private $expire = 3600; 
	
	public $dirCache = DIR_CACHE."caches/";

  	public function __construct() {
		if (!file_exists($this->dirCache)) {
			mkdir($this->dirCache, 0755, true);
		}
		$this->expire = (defined('CACHE_EXPIRE')) ? CACHE_EXPIRE : 3600;
		$files = glob($this->dirCache . '*.cache');
		
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
		}
  	}
	
	public function verify($key) {
		$files = glob($this->dirCache . '*.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');

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
		$files = glob($this->dirCache . '*.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');

		if ($files) {
			$cache = file_get_contents($files[0]);
			
			return unserialize($cache);
		}
	}

  	public function set($key, $value, $expire = true) {
    	$this->delete($key);
		
		$exp = ($expire == true) ? (time() + $this->expire) : 0;
		
		$file = $this->dirCache  . $exp . '.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache';

    	return file_put_contents($file, serialize($value));
		
  	}
	
  	public function delete($key) {
		$files = glob($this->dirCache . '*.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');
		
		if ($files) {
    		foreach ($files as $file) {
      			if (file_exists($file)) {
					unlink($file);
					clearstatcache();
				}
    		}
		}
  	}
}