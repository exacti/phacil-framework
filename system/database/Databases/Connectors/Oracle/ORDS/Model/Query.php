<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Connectors\Oracle\ORDS\Model;

use Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\HandleInterface;
use Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\Query as QueryApi;
use Phacil\Framework\Databases\Connectors\Oracle\ORDS\Helper\Data as DataHelper;

class Query implements QueryApi {

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\HandleInterface
	 */
	private $handle;

	/**
	 * 
	 * @var string
	 */
	private $sql;

	/**
	 * 
	 * @var int
	 */
	private $num_rows = 0;

	/**
	 * 
	 * @var array
	 */
	private $items = [];

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Helper\Data
	 */
	private $helper;

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Connector
	 */
	private $connector;

	/**
	 * 
	 * @var \Phacil\Framework\Factory
	 */
	private $handleFactory;

	/**
	 * 
	 * @param \Phacil\Framework\Factory $Factory 
	 * @param \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Helper\Data $helper 
	 * @return $this 
	 */
	public function __construct(
		\Phacil\Framework\Factory $Factory,
		DataHelper $helper
	) {
		$this->handleFactory = $Factory;
		$this->handleFactory->setClass(HandleInterface::class);
		$this->helper = $helper;

		return $this;
	}

	/**
	 * @param \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Connector $connector 
	 * @return $this 
	 */
	public function setConnector(\Phacil\Framework\Databases\Connectors\Oracle\ORDS\Connector $connector){
		$this->connector = $connector;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function prepareSQL($sql, $bindParams = [])
	{
		// Percorre os parâmetros a serem vinculados
		foreach ($bindParams as $key => $value) {
			// Detecta o tipo do valor e formata conforme necessário
			$formattedValue = $this->helper->escape($value);

			if(!is_string($key))
				$sql = preg_replace('/\?/', $formattedValue, $sql, 1);
			else {
				$sql = str_replace($key, $formattedValue, $sql);
			}
		}

		$this->sql = $sql;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		if(empty($this->sql)){
			throw new \Phacil\Framework\Exception\RuntimeException('Query is empty');
		}

		$this->handle = $this->handleFactory->create([$this->connector]);

		$executed = $this->handle->execute($this->sql);

		if($executed){
			$executed = \Phacil\Framework\Json::decode($executed);

			$resultEnd = isset($executed['items']) ? end($executed ['items']) : null;

			$resultSet = isset($resultEnd['resultSet']) ? $resultEnd['resultSet'] : null;

			if($resultSet) {
				$this->num_rows = $resultSet["count"];
				$this->items = $resultSet["items"];
			} elseif (isset($resultEnd["result"])) {
				$this->num_rows = $resultEnd["result"];
			}

			return $this;	
		} else {
			throw new \Phacil\Framework\Exception\RuntimeException($this->handle->error());
		}
	}

	/** {@inheritdoc} */
	public function getNumRows() {
		return $this->num_rows;
	}

	/** {@inheritdoc}  */
	public function getItems() {
		return $this->items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function close() {
		if($this->handle){
			$this->handle->close();

			unset($this->handle);
		}
		//$this->handle = \Phacil\Framework\Registry::getInstance()->create(HandleInterface::class);
	}

	/** @inheritdoc  */
	public function getError(){
		return $this->handle->error();
	}
}