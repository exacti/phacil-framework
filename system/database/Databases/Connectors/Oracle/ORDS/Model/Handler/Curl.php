<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Connectors\Oracle\ORDS\Model\Handler;

use Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api\HandleInterface;
use Phacil\Framework\Databases\Connectors\Oracle\ORDS\Connector;

/**
 * Tested with Apex 23+
 * 
 * @package Phacil\Framework\Databases\Connectors\Oracle\ORDS\Model\Handler
 */
class Curl implements HandleInterface {
	/**
	 * 
	 * @var resource|false
	 */
	private $curl;

	/**
	 * @var \Phacil\Framework\Databases\Connectors\Oracle\ORDS\Connector
	 */
	private $conector;

	public function __construct(
		Connector $conector
	)
	{
		$this->curl = curl_init();
		$this->conector = $conector;

		$generatedAuth = base64_encode($this->conector->getUser().":".$this->conector->getPass());

		$this->setOption(CURLOPT_PORT, $this->conector->getPort());
		$this->setOption(CURLOPT_URL, $this->conector->getHost()."/_/sql");
		$this->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->setOption(CURLOPT_MAXREDIRS, 10);
		$this->setOption(CURLOPT_TIMEOUT, 30);
		$this->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		$this->setOption(CURLOPT_CUSTOMREQUEST, 'POST');
		$this->setOption(CURLOPT_HTTPHEADER, [
			"Accept: */*",
			sprintf("Authorization: Basic %s", $generatedAuth),
			"Content-Type: application/sql"
		]);
	}

	public function setOption($option, $value)
	{
		curl_setopt($this->curl, $option, $value);
	}

	public function exec()
	{
		return curl_exec($this->curl);
	}

	public function error()
	{
		return curl_error($this->curl);
	}

	public function close()
	{
		curl_close($this->curl);
	}

	public function execute($sql) {
		$post = [
			'$' => $sql
		];
		$this->setOption(CURLOPT_POSTFIELDS, $post);

		return $this->exec();
	}
}