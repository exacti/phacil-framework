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
interface MailInterface {

	const PROTOCOL_SMTP = 'smtp';

	const PROTOCOL_MAIL = 'mail';

	const XMAILER_SIGN = 'Phacil Framework Mailer Component';

	/** @return bool  */
	public function getVerp();

	/** @return string  */
	public function getParameter();

	/** @return \Phacil\Framework\Config  */
	public function getConfig();

	/**
	 * @param string $to 
	 * @return $this 
	 */
	public function setTo($to);

	/** @return string  */
	public function getTo();

	/**
	 * 
	 * @param string $from 
	 * @return $this 
	 */
	public function setFrom($from);

	/** @return string  */
	public function getFrom();

	/**
	 * @param string $sender 
	 * @return $this 
	 */
	public function setSender($sender);

	/** @return string  */
	public function getSender();

	/**
	 * @param string $replyTo 
	 * @return $this 
	 */
	public function setReplyTo($replyTo);

	/** @return string  */
	public function getReplyTo();

	/**
	 * @param string $subject 
	 * @return $this 
	 */
	public function setSubject($subject);

	/** @return string  */
	public function getSubject();

	/**
	 * @param string $text 
	 * @return $this 
	 */
	public function setText($text);

	/** @return string  */
	public function getText();

	/**
	 * @param string $html 
	 * @return $this 
	 */
	public function setHtml($html);

	/** @return string  */
	public function getHtml();

	/**
	 * @param string $file 
	 * @param string $filename 
	 * @return $this 
	 */
	public function addAttachment($file, $filename = '');

	/** @return array  */
	public function getAttachments();

	/** @return bool  */
	public function send();
}