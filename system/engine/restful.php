<?php
/**
 * @copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework;

use Phacil\Framework\Controller;

/**
 * Create a simple anda faster REST API controller.
 * 
 * @package Phacil\Framework
 * @since 2.0.0
 * @abstract
 * @api
 */
abstract class RESTful extends Controller {

	/**
	 * The output content type
	 * 
	 * @var string
	 */
	public $contentType = 'application/json';

	/**
	 * 
	 * @var string
	 */
	public $acceptType = 'application/json';

	/**
	 * 
	 * @var int
	 */
	public $error_code = 404;

	/**
	 * 
	 * @var string
	 */
	public $error_msg = 'This service not have implemented the %s method.';

	/**
	 * 
	 * @var string[]
	 */
	public $HTTPMETHODS = [ 'GET', 'CONNECT', 'HEAD', 'PUT', 'DELETE', 'TRACE', 'POST', 'OPTIONS', 'PATCH'];

	/**
	 * 
	 * @return void 
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	function index() {
		$method = (Request::METHOD());

		if (in_array($method, $this->HTTPMETHODS) && is_callable(array($this, $method))) {
			$r = new ReflectionMethod($this, $method);
			$params = [];

			$comment_string = $r->getDocCommentParse();

			$phpDocParams = ($comment_string) ? $comment_string->getParams() : false;
			
			foreach($r->getParameters() as $key => $value) {
				switch (strtoupper( $method)) {
					case 'GET':
						$data = Request::GET($value->getName());
						break;
					
					case 'HEAD':
						$data = Request::HEADER($value->getName());
						break;
					
					default:
						try {
							$data = (Request::POST($value->getName())) ?: Request::INPUT($value->getName());
						} catch (\Exception $th) {
							return $this->__callInterrupt($th->getMessage());
						}
						break;
				}

				/**
				 * check if have a sufficiente data for the request
				 */
				if($data === null) {
					if(!$value->isOptional()){
						return $this->__callInterrupt($value->getName(). " is required.");
					}
				}

				if($data !== null && $phpDocParams && isset($phpDocParams['param']) && is_array($phpDocParams['param'])){
					$type = (isset($phpDocParams['param']['$'.$value->getName()])) ? $phpDocParams['param']['$' . $value->getName()]['type']: false;
					if($type){
						if((is_array($type) && !in_array(gettype($data), $type)) || (gettype($data) != $type)){
							$invalidDataType = true;

							if(is_array($type)){
								foreach ($type as $avalType) {
									if(self::__testType( $avalType, $data)) {
										$invalidDataType = false;
										break;
									}
								}
							} else {
								if(self::__testType($type, $data)) {
									$invalidDataType = false;
								}
							}
							
							if($invalidDataType){
								return $this->__callInterrupt($value->getName() . " need to be: ".(is_array($type) ? implode(', ', $type) : $type).". You give: ".gettype($data).".");
							}
							
						} 

					}
				}

				if($data){
					$params[$value->getName()] = $data; 
				};
			};

			try {
				//code...
				call_user_func_array(array($this, $method), $params);
			} catch (\Throwable $th) {
				if(get_class($th) == 'TypeError'){
					new Exception($th->getMessage(), $th->getCode());
					return $this->__callInterrupt($th->getMessage());
				} else {
					throw new Exception($th->getMessage(), $th->getCode());
				}
				
			} catch (Exception $e) {
				throw new Exception($e->getMessage(), $e->getCode());
			}
		} else {
			$this->__callNotFound($method);
		}
	}

	/**
	 * Return true or false for data type
	 * 
	 * @param string $type Type to test
	 * @param string $data Data to test
	 * @return bool 
	 */
	static function __testType($type, $data){ 

		switch ($type) {
			case 'mixed':
				return true;
				break;
				
			case 'int':
			case 'string':
			case 'array':
			case 'integer':
			case 'bool':
			case 'double':
			case 'float':
			case 'long':
			case 'null':
			case 'numeric':
			case 'scalar':
			case 'real':
				return call_user_func("is_" . $type, $data);
				break;
			
			default:
				return false;
				break;
		}

	}

	/**
	 * Not found default method
	 * 
	 * @param string $method 
	 * @param mixed $args 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	protected function __callNotFound($method, $args = null) {
		$this->response->code($this->error_code);
		$this->data['error'] = sprintf($this->error_msg, $method);

		$this->out();
	}

	/**
	 * Interrupt method with a exit.
	 * 
	 * @param string $msg 
	 * @param int $code 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	protected function __callInterrupt($msg, $code = 400) {
		$this->error_msg = $msg;
		$this->error_code = 400;
		$method = 'JSONERROR';
		$this->__callNotFound($method);
		return;
	}

	/**
	 * 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	protected function JSONERROR() {
		$this->out();
	}

	/**
	 * Deafult method to OPTIONS HTTP call.
	 * 
	 * This method list in HTTP header a "Allow" tag with methods implemented and accesibles.
	 * 
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/OPTIONS
	 * 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	protected function OPTIONS(){
		$methods = [];
		foreach ($this->HTTPMETHODS as $method) {
			if (is_callable(array($this, $method)))
				$methods[] = $method;
		}

		$this->response->addHeader('Allow', implode(", ", $methods));
		$this->data['allow'] =  $methods;

		$this->out();
	}

	/**
	 * The default and automated output method. All itens in the $this->data are rendered in JSON format.
	 * 
	 * @param bool $commonChildren (optional)
	 * @return \Phacil\Framework\Response 
	 * @throws Exception 
	 */
	protected function out($commonChildren = true)
	{
		$this->response->addHeader('Content-Type', $this->contentType);
		if($this->acceptType)
			$this->response->addHeader('Accept', $this->acceptType);

		return $this->response->setOutput(JSON::encode($this->data));
	}
}