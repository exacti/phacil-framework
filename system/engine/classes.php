<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** 
 * This class return the loaded classes and functions in HTML or array format.
 * 
 * @uses Classes()->classes() to view loaded classes
 * @uses Classes()->functions() to view the funcions of loaded classes.
 * 
 * The method exists('class') check if a class exists and is most compatible instead PHP native function.
 * 
 * @uses Classes()->exists('class') to check if a class exists.
 * 
 * @package Phacil\Framework
 * @since 1.5.0 
 */
final class Classes {
	
	/**
	 * 
	 * @var string|null
	 */
	private $format;
	
	/**
	 * @param string|null $format Define if the output is an array or HTML format
	 * @return void 
	 */
	public function __construct($format = NULL) {
		
		$this->format = $format;
		
	}
	
	/**
	 * Return the loaded classes 
	 * @param string|null $origin 
	 * @return string|array
	 */
	public function classes($origin = NULL){
		$mClass = get_declared_classes();
		
		$pegaKey = 0;

		$pegaClass = [];
		
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
	
	/** 
	 * Return the functions of loaded classes
	 * 
	 * @return array|string  */
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

	/**
	 * Check the class exists
	 * 
	 * @param string $class 
	 * @return bool 
	 */
	public function exists($class)
	{
		return class_exists($class);
	}
	
	
}
