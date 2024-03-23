<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Connectors\Oracle\ORDS\Api;

interface Query {
	/**
	 * @param string $sql 
	 * @param array $bindParams 
	 * @return $this 
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException 
	 */
	public function prepareSQL($sql, $bindParams = []);

	/**
	 * @return $this 
	 * @throws \Phacil\Framework\Exception\RuntimeException 
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException 
	 */
	public function execute();

	/** @return int  */
	public function getNumRows();

	/** @return array  */
	public function getItems();

	/**
	 * @return void 
	 * @throws \ReflectionException 
	 * @throws \Exception 
	 * @throws \Phacil\Framework\Exception 
	 * @throws \Phacil\Framework\Exception\ReflectionException 
	 */
	public function close();

	/** @return string  */
	public function getError();
}