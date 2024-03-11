<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework\Databases\Api;

 use Phacil\Framework\Api\Log as FrameworkLogInterface;

 /**
  * @since 2.0.0
  * @package Phacil\Framework\Databases\Api
  * @api
  */
 interface LogInterface extends FrameworkLogInterface {
	const LOG_FILE_NAME = 'dbquery.log';
 }