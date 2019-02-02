<?php
final class DB {
	private $driver;
	
	private $cachePrefix = "SQL_";
	
	public function __construct($driver, $hostname, $username, $password, $database) {
		if (file_exists(DIR_DATABASE . $driver . '.php')) {
			require_once(DIR_DATABASE . $driver . '.php');
		} else {
			exit('Error: Could not load database file ' . $driver . '!');
		}
				
		$this->driver = new $driver($hostname, $username, $password, $database);
	}
		
  	public function query($sql, $cacheUse = true) {
		
		if(defined('SQL_CACHE') && SQL_CACHE == true && $cacheUse == true) {
			
			return $this->Cache($sql);
			
		} else {
			
			return $this->driver->query($sql);
		}		
		
  	}
	
	public function escape($value) {
		return $this->driver->escape($value);
	}
	
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	public function getLastId() {
		return $this->driver->getLastId();
  	}	
	
	private function Cache($sql) {
		if(class_exists('Caches')) {
			$cache = new Caches();
		
			if (stripos($sql, "select") !== false) {

				if($cache->check($this->cachePrefix.md5($sql))) {
					
					return $cache->get($this->cachePrefix.md5($sql));
					
				} else {
					$cache->set($this->cachePrefix.md5($sql), $this->driver->query($sql));

					return $this->driver->query($sql);
				}
			} else {
				return $this->driver->query($sql);
			}
		} else {
			return $this->driver->query($sql);
		}
	}
}
?>