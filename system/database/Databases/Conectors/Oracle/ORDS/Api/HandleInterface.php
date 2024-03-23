<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Databases\Conectors\Oracle\ORDS\Api;

interface HandleInterface {
	public function __construct(\Phacil\Framework\Databases\Conectors\Oracle\ORDS\Conector $conector);

	public function setOption($option, $value);

	public function exec();

	public function error();

	public function close();

	public function execute($sql);
}