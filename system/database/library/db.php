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

    public function pagination($sql, $pageNum_exibe = 1, $maxRows_exibe = 10, $cache = true, $sqlTotal = null){

        if (($pageNum_exibe >= 1)) {
            $pageNum_exibe = $pageNum_exibe-1;
        }
        $startRow_exibe = $pageNum_exibe * $maxRows_exibe;

        $query_exibe = $sql;

        $query_limit_exibe = sprintf("%s LIMIT %d, %d", $query_exibe, $startRow_exibe, $maxRows_exibe);

        $exibe = $this->query($query_limit_exibe, $cache);

        $re = '/^(SELECT \*)/i';

        $all_exibe_query = ($sqlTotal != null) ? $sqlTotal : ((preg_match($re, $query_exibe)) ? preg_replace($re, "SELECT COUNT(*) as __TOTALdeREG_DB_Pagination", $query_exibe) : $query_exibe);

        $all_exibe = $this->query($all_exibe_query, $cache);
        $totalRows_exibe = (isset($all_exibe->row['__TOTALdeREG_DB_Pagination'])) ? $all_exibe->row['__TOTALdeREG_DB_Pagination'] : $all_exibe->num_rows;

        if($totalRows_exibe <= 0){
            $all_exibe_query = $query_exibe;
            $all_exibe = $this->query($all_exibe_query, $cache);
            $totalRows_exibe = (isset($all_exibe->row['__TOTALdeREG_DB_Pagination'])) ? $all_exibe->row['__TOTALdeREG_DB_Pagination'] : $all_exibe->num_rows;
        }

        $totalPages_exibe = ceil($totalRows_exibe/$maxRows_exibe);

        $exibe->totalPages_exibe = $totalPages_exibe;
        $exibe->totalRows_exibe = $totalRows_exibe;
        $exibe->pageNum_exibe = $pageNum_exibe+1;

        return $exibe;
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

	public function createSubBase($nome, $object) {

        $this->$nome = $object;
    }
}
