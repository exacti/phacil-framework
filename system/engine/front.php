<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Front {

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
	 * @param ActionSystem $pre_action 
	 * @return void 
	 */
	public function addPreAction(\Phacil\Framework\ActionSystem $pre_action) {
		$this->pre_action[] = $pre_action;
	}
	
  	
  	/**
  	 * @param Action $action 
  	 * @param Action $error 
  	 * @return void 
  	 */
  	public function dispatch(\Phacil\Framework\Action $action, \Phacil\Framework\Action $error) {
		$this->error = $error;
			
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
    
	private function execute(object $action) {
		$file = $action->getFile();
		$class = $action->getClass();
		$classAlt = $action->getClassAlt();
		$method = $action->getMethod();
		$args = $action->getArgs();

		$action = '';

		$c = get_declared_classes();

		if (file_exists($file)) {
			require_once($file);

			try {
				$controller = new $classAlt['class']($this->registry);
				$action->setClass($classAlt['class']);
				array_shift($classAlt['class']);
			} catch (\Throwable $th) {
				foreach($classAlt as $classController){
					try {
						$controller = new $classController($this->registry);
						$action->setClass($classController);
					} catch (\Throwable $th) {
						//throw $th;
					}
				}
			}

			if(!$controller) {

				try {
					$controller = new $class($this->registry);
					
				} catch (\Throwable $th) {
					$e = array_diff(get_declared_classes(), $c);
					try {
						$classController = end($e);
						$controller = new $classController($this->registry);
						$action->setClass($classController);
					} catch (\Throwable $th) {
						foreach($e as $classController){
							try {
								$controller = new $classController($this->registry);
								$action->setClass($classController);
							} catch (\Throwable $th) {
								//throw $th;
							}
						}
					}
					
				}
			}
			
			if (is_callable(array($controller, $method))) {
				$action = call_user_func_array(array($controller, $method), $args);
			} else {
				$action = $this->error;
			
				$this->error = '';
			}
		} else {
			$action = $this->error;
			
			$this->error = '';
		}
		
		return $action;
	}
}
