<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Conectors\Oracle\ORDS\Model;

use Phacil\Framework\Databases\Conectors\Oracle\ORDS\Conector;
use Phacil\Framework\Databases\Conectors\Oracle\ORDS\Api\HandleInterface;

class Query {

	/**
	 * 
	 * @var \Phacil\Framework\Databases\Conectors\Oracle\ORDS\Conector
	 */
	private $conector;

	private $handle;

	private $sql;

	private $num_rows = 0;

	private $items = [];

	public function __construct(
		Conector $conector,
		HandleInterface $handle
	) {
		$this->conector = $conector;
		$this->handle = $handle;
	}

	/**
	 * @param string $sql 
	 * @param array $bindParams 
	 * @return $this 
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException 
	 */
	public function prepareSQL($sql, $bindParams = [])
	{
		// Percorre os parâmetros a serem vinculados
		foreach ($bindParams as $key => $value) {
			// Detecta o tipo do valor e formata conforme necessário
			$formattedValue = $this->conector->escape($value);

			if(!is_string($key))
				$sql = preg_replace('/\?/', $formattedValue, $sql, 1);
			else {
				$sql = str_replace($key, $formattedValue, $sql);
			}
		}

		$this->sql = $sql;

		return $this;
	}

	public function execute() {
		if(empty($this->sql)){
			throw new \Phacil\Framework\Exception\RuntimeException('Query is empty');
		}

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

	public function getNumRows() {
		return $this->num_rows;
	}

	public function getItems() {
		return $this->items;
	}

	public function close() {
		$this->handle->close();

		unset($this->handle);
		$this->handle = \Phacil\Framework\Registry::getInstance()->create(HandleInterface::class);
	}

	public function getError(){
		return $this->handle->error();
	}
}