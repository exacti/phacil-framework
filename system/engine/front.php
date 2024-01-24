<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Interfaces\Front as frontInterface;

/** @package Phacil\Framework */
final class Front implements frontInterface {

	/**
	 * 
	 * @var Registry
	 */
	protected $registry;

	/**
	 * 
	 * @var array
	 */
	protected $pre_action = array();

	protected $error;
	
	/**
	 * 
	 * @param Registry $registry 
	 * @return void 
	 */
	public function __construct(Registry $registry) {
		$this->registry = $registry;
	}
	
	
	/**
	 * @param \Phacil\Framework\Interfaces\Action $pre_action 
	 * @return void 
	 */
	public function addPreAction(\Phacil\Framework\Interfaces\Action $pre_action) {
		$this->pre_action[] = $pre_action;
	}
	
  	
  	/**
  	 * @param Action $action 
  	 * @param string $error 
  	 * @return void 
  	 */
  	public function dispatch(\Phacil\Framework\Interfaces\Action $action, $error) {
		$this->error = $error;

		$this->registry->set('route', $action->getRoute());
			
		foreach ($this->pre_action as $pre_action) {
			$result = $this->execute($pre_action);
					
			if ($result) {
				$action = $result;
				
				break;
			}
		}
			
		while ($action) {
			$action = $this->execute($action);
		}
  	}

	/**
	 * @param object $action 
	 * @return \Phacil\Framework\Interfaces\Action|\Phacil\Framework\Interfaces\Controller|null
	 * @throws Exception 
	 */
	private function execute(\Phacil\Framework\Interfaces\Action $action) {
		$file = $action->getFile();
		$class = $action->getClass();
		$classAlt = $action->getClassAlt();
		$method = $action->getMethod();
		$args = $action->getArgs();

		unset($action);

		if ($file && file_exists($file)) {
			require_once($file);

			foreach($classAlt as $classController){
				try {
					if(class_exists($classController)){
						
						$action = $this->callController($this->injectionClass($classController), $method, $args);
						
						/* if(!is_subclass_of($controller, 'Phacil\Framework\Controller')){
							throw new Exception('PHACIL ERROR: Controller '. get_class($controller) . ' doesn\'t have Phacil\Framework\Controller implemented');
						} */

						break;
					}
				} catch (\Phacil\Framework\Exception\Throwable $th) {
					throw $th;
				}
			}
			
			/* try {
				if (is_callable(array($controller, $method))) {
					$action = call_user_func_array(array($controller, $method), $args);
				} else {
					$action = new Action($this->error);
				
					$this->error = '';
					throw new Exception("The controller can't be loaded", 1);
				}
			} catch (\Phacil\Framework\Exception\Throwable $th) {
				//throw $th;
				$action = new Action($this->error);
			
				$this->error = '';

				throw new Exception("The controller can't be loaded: ".$th->getMessage(), $th->getCode(), $th);
				
			} */
			
		} elseif(!$file && isset($classAlt['class'])) {
			try {
				$this->injectionClass($classController);
				$action = $this->callController(new $classAlt['class']($this->registry), $method, $args);

				/* if (!is_subclass_of($controller, 'Phacil\Framework\Controller')) {
					throw new Exception('PHACIL ERROR: Controller ' . get_class($controller) . '  doesn\'t have Phacil\Framework\Controller implemented');
				}

				if(!is_callable(array($controller, $method))) {
					$action = new Action($this->error);

					$this->error = '';

					new Exception("PHACIL ERROR: Controller class " . get_class($controller) . "->".$method."() is not a callable function");
				} else {
					$action = call_user_func_array(array($controller, $method), $args);
				} */
			} catch (\Phacil\Framework\Exception\Throwable $th) {
				throw ($th);
			}
		}else {
			$action = new Action($this->error);
			
			$this->error = '';
		}
		
		return $action;
	}

	/**
	 * 
	 * @param \Phacil\Framework\Interfaces\Controller $controller 
	 * @return \Phacil\Framework\Interfaces\Controller|\Phacil\Framework\Interfaces\Action|null 
	 * @throws \Phacil\Framework\Exception 
	 */
	protected function callController(\Phacil\Framework\Interfaces\Controller $controller, $method, $args = array()) {
		try {
			//code...
			if (is_callable(array($controller, $method))) {
				$action = call_user_func_array(array($controller, $method), $args);
			} else {
				$action = new Action($this->error);

				$this->error = '';
				//throw new Exception("The controller can't be loaded", 1);
				new Exception("PHACIL ERROR: Controller class " . get_class($controller) . "->" . $method . "() is not a callable function");
			}
			return $action;
		} catch (\Exception $th) {
			throw new Exception($th->getMessage(), $th->getCode(), $th);
			//throw $th;
		}
	}

	protected function injectionClass($class){
		/* $refClass = new ReflectionClass($class);

		if(!$refClass->getConstructor()){
			if($refClass->hasMethod('getInstance') && $refClass->getMethod('getInstance')->isStatic()){
				return $refClass->getMethod('getInstance')->invoke(null);
			}

			return $refClass->newInstanceWithoutConstructor();
		}

		try {
			if ($autoInstance = $this->registry->getInstance($class))
				return $autoInstance;
		} catch (\Throwable $th) {
			//throw $th;
		} */
		
			
		return $this->registry->injectionClass($class);
	}
}
