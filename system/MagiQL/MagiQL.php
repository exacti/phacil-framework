<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\MagiQL\Builder;

class MagiQL extends Builder {

	const SELECT_KEY = 'SELECT';
	const FROM_KEY = 'FROM';
	const WHERE_KEY = 'WHERE';
	const ORDER_KEY = 'ORDER';
	const GROUP_KEY = 'GROUP';
	const JOIN_KEY = 'JOIN';

	/**
	 * 
	 * @var \Phacil\Framework\Database
	 */
	private $db;

	private $queryObj;

	/**
	 * 
	 * @var array
	 */
	protected $queryArray;

	public function __construct(\Phacil\Framework\Database $db) {
		$this->db = $db;

		$this->queryObj = new Builder();
		parent::__construct();
	}

	public function __call($name, $arguments = array()){

		return call_user_func_array([$this->queryObj, $name], $arguments);
	}

}