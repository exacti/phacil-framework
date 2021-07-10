<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

final class Translate {
	
	private $autoLang;
	private $request;
	private $session;
	
	public function __construct(){
		
		$this->request = new Request();
		
		$this->session = new Session();
		
		$this->autoLang = (isset($this->session->data['lang'])) ? $this->session->data['lang'] : NULL;
				
		$this->cookie = (isset($this->request->cookie['lang'])) ? $this->request->cookie['lang'] : NULL;
		
		$this->cache = new Caches();
				
		if($this->autoLang != NULL) {
			setcookie("lang", ($this->autoLang), strtotime( '+90 days' ));
		}

		
	}
	
	public function translation ($value, $lang = NULL) {
		
		global $db;
		
		//$cache = new Caches();
				
		$lang = ($lang != NULL) ? $lang : $this->autoLang;
		
		if($this->cache->check("lang_".$lang."_".md5($value))) {
			return $this->cache->get("lang_".$lang."_".md5($value));
			
		} else {
			$sql = "SELECT * FROM translate WHERE text = '".$db->escape($value)."'";
			$result = $db->query($sql, false);

			if ($result->num_rows == 1) {
				if(isset($result->row[$lang]) and $result->row[$lang] != "") {
					$this->cache->set("lang_".$lang."_".md5($value), $result->row[$lang], false);
					return $result->row[$lang]; //valid translation present
				} else {
					return $value;
				}

			} else { //message not found in the table
				//add unfound message to the table with empties translations
				$this->insertBaseText($value);

				return $value;
			}
		}
		
		
	}
	
	public function insertBaseText ($value){
		global $db;
		
		$sql = $db->query("INSERT INTO translate SET text='".$db->escape($value)."'");
	}
}