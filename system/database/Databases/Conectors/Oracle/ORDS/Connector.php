<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Conectors\Oracle\ORDS;

class Connector {

	private $host;

	private $port;

	private $user;

	private $pass;

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

	public function escape($value)
	{
		$formattedValue = null;

		switch (gettype($value)) {
			case 'integer':
				$formattedValue = $value; // Números inteiros não precisam de formatação
				break;
			case 'double':
				$formattedValue = sprintf('%F', $value); // Formata números decimais
				break;
			case 'boolean':
				$formattedValue = $value ? 'TRUE' : 'FALSE'; // Converte booleanos para strings 'TRUE' ou 'FALSE'
				break;
			case 'NULL':
				$formattedValue = 'NULL'; // Valores nulos
				break;
			case 'object':
				$formattedValue = "'" . serialize($value) . "'"; // Valores nulos
				break;
			case 'array':
				$formattedValue = "'" . \Phacil\Framework\Json::encode($value) . "'"; // Valores nulos
				break;
			default:
				// Escapa as aspas simples e adiciona aspas simples ao redor de strings
				$formattedValue = "'" . str_replace("'", "''", $value) . "'";
		}

		return $formattedValue;
	}
}