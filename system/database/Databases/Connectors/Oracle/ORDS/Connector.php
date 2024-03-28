<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Connectors\Oracle\ORDS;

/**
 * @since 2.0.0
 * @package Phacil\Framework\Databases\Connectors\Oracle\ORDS
 */
class Connector {

	private $host;

	private $port;

	private $user;

	private $pass;

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\Query
	 */
	private $queryExecutor;

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\HandleInterface
	 */
	private $queryHandler;

	public function __construct($url = 'localhost', $port = 8181, $user = null, $pass = null){
		$this->host = $url;

		$this->port = $port;

		$this->user = $user;

		$this->pass = $pass;

		return $this;
	}

	public function getHost(){
		return $this->host;
	}

	public function getPort(){
		return $this->port;
	}

	public function getUser(){
		return $this->user;
	}

	public function getPass(){
		return $this->pass;
	}

	/**
	 * @return \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\Query 
	 * @throws \Phacil\Framework\Exception 
	 * @throws \Phacil\Framework\Exception\ReflectionException 
	 */
	public function createStatement(){
		/** @var \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\Query */
		$this->queryExecutor = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\Query::class);

		return $this->queryExecutor->setConnector($this);
	}

	/** @return \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\Query  */
	public function getStatement() {
		return $this->queryExecutor;
	}
}