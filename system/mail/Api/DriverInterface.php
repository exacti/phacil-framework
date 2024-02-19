<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Mail\Api;

/**
 * @since 2.0.0
 * @package Phacil\Framework\Mail\Api
 * @api
 */
interface DriverInterface {

	const CONFIG_MAIL_PROTOCOL = 'config_mail_protocol';

	const CONFIG_SMTP_PORT = 'config_smtp_port';

	const CONFIG_SMTP_HOSTNAME = 'config_smtp_host';

	const CONFIG_SMTP_USERNAME = 'config_smtp_username';

	const CONFIG_SMTP_PASSWORD = 'config_smtp_password';

	const CONFIG_SMTP_TIMEOUT = 'config_smtp_timeout';

	/**
	 * 
	 * @var string
	 */
	const NEWLINE = "\n";

	/**
	 * 
	 * @var string
	 */
	const CRLF = "\r\n";

	/**
	 * 
	 * @var bool
	 */
	const VERP = false;

	/**
	 * @param string $to 
	 * @param string $message 
	 * @param string $header 
	 * @return bool 
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function send($to, $message, $header);
}
