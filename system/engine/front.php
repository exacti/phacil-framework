<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Interfaces\Front as frontinterface;

//use Exception;

/** @package Phacil\Framework */
final class Front implements frontinterface {

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
	 * @return \Phacil\Framework\Interfaces\Action 
	 * @throws Exception 
	 */
	private function execute($action) {
		$file = $action->getFile();
		$class = $action->getClass();
		$classAlt = $action->getClassAlt();
		$method = $action->getMethod();
		$args = $action->getArgs();

		unset($action);

		if (file_exists($file)) {
			require_once($file);

			foreach($classAlt as $classController){
				try {
					if(class_exists($classController)){
						$controller = new $classController($this->registry);
						
						break;
					}
				} catch (\Exception $th) {
					//throw $th;
				}
			}
			
			try {
				if (is_callable(array($controller, $method))) {
					$action = call_user_func_array(array($controller, $method), $args);
				} else {
					$action = new Action($this->error);
				
					$this->error = '';
					throw new \Exception("The controller can't be loaded", 1);
				}
			} catch (\Exception $th) {
				//throw $th;
				$action = new Action($this->error);
			
				$this->error = '';

				throw new \Exception("The controller can't be loaded", 1);
				
			}
			
		} else {
			$action = new Action($this->error);
			
			$this->error = '';
		}
		
		return $action;
	}
}
