<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Classes {
	
	private $format;
	
	public function __construct($format = NULL) {
		
		$this->format = $format;
		
	}
	
	/**
	 * @param string|null $origin 
	 * @return string
	 */
	public function classes($origin = NULL){
		$mClass = get_declared_classes();
		
		$pegaKey = 0;
		
		foreach($mClass as $key => $value){
			if($value == 'startEngineExacTI'){
				$pegaKey = $key;
			}
			if($pegaKey != 0 and $key > $pegaKey){
				$pegaClass[] .= $value;
			}
		}
		
		if($this->format == "HTML" && $origin != 'intern'){
			$pegaClassD = $pegaClass;
			$pegaClass = '';
			foreach($pegaClassD as $value) {
				$pegaClass .= '<strong>'.$value.'</strong><br>';
				
			}
		}
		
		
		return($pegaClass);
	}
	
	/** @return array|string  */
	public function functions(){
		$classes = $this->classes('intern');
		
		$functions = array();
		
		foreach($classes as $key => $value){
			$functions = array_merge($functions, array($value => get_class_methods($value)));
		}
		
		if($this->format == "HTML"){
			$functions = "";
			foreach($classes as $value) {
				$functions .= '<hr><strong>'.$value.'</strong><br>';
				$subFunc = get_class_methods($value);
				foreach($subFunc as $value) {
					$functions .= '<em>'.$value.'</em><br>';
				}
			}
		}
		
		return $functions;
		
	}
	
	
}