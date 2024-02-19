<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Mail\Drivers;

class Native implements \Phacil\Framework\Mail\Api\DriverInterface {
	private $from;

	private $parameter;

	private $subject;

	/**
	 * 
	 * @var \Phacil\Framework\Mail\Api\MailInterface
	 */
	private $mail;

	/**
	 * @param \Phacil\Framework\Mail\Api\MailInterface $mail 
	 * @return void 
	 */
	public function __construct(\Phacil\Framework\Mail\Api\MailInterface $mail){
		$this->mail = $mail;
	}

	protected function validate() {
		$this->from = $this->mail->getFrom();
		$this->subject = $this->mail->getSubject();
		$this->parameter = $this->mail->getParameter();

		if(!$this->from || !is_string($this->from)) 
			throw new \Phacil\Framework\Exception\InvalidArgumentException('Requires a valid FROM configuration');
	}

	/**
	 * {@inheritdoc}
	 */
	public function send($to, $message, $header){
		$this->validate();

		ini_set('sendmail_from', $this->from);

		if (!empty($this->parameter)) {
			return mail($to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header, $this->parameter);
		} else {
			return mail($to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header);
		}
	}

}