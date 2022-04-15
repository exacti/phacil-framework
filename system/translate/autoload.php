<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phacil\Framework\Registry;

/** @package Phacil\Framework */

final class Translate {
	
	/**
	 * 
	 * @var string|null
	 */
	private $autoLang;

	/**
	 * 
	 * @var \Phacil\Framework\Session
	 */
	private $session;

	/**
	 * 
	 * @var \Phacil\Framework\Database
	 */
	private $db;

	/**
	 * 
	 * @var \Phacil\Framework\Caches
	 */
	protected $cache;
	
	public function __construct(){
		
		$this->session = Registry::getInstance()->session;
		
		$this->autoLang = (isset($this->session->data['lang'])) ? $this->session->data['lang'] : NULL;
				
		$this->cookie = (Request::COOKIE(['lang'])) ?: NULL;
		
		$this->cache = Registry::getInstance()->cache;

		$this->db = Registry::getInstance()->db;
				
		if($this->autoLang != NULL) {
			setcookie("lang", ($this->autoLang), strtotime( '+90 days' ));
		}
		
	}
	
	/**
	 * @param string $value 
	 * @param string|null $lang 
	 * @return string 
	 * @throws PhpfastcacheInvalidArgumentException 
	 */
	public function translation ($value, $lang = NULL) {

		$lang = ($lang != NULL) ? $lang : $this->autoLang;
		
		if($this->cache->check("lang_".$lang."_".md5($value))) {
			return $this->cache->get("lang_".$lang."_".md5($value));
			
		} else {
			$sql = "SELECT * FROM translate WHERE text = '".$this->db->escape($value)."'";
			$result = $this->db->query($sql, false);

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
	
	/**
	 * @param string $value 
	 * @return void 
	 */
	public function insertBaseText ($value){		
		$this->db->query("INSERT INTO translate SET text='".$this->db->escape($value)."'");
	}
}