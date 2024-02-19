<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Mail\Api\MailInterface;
use Phacil\Framework\Config;

/** 
 * @since 1.0.0
 * @package Phacil\Framework 
 */
class Mail implements MailInterface {

	/**
	 * 
	 * @var string
	 */
	protected $to;

	/**
	 * 
	 * @var string
	 */
	protected $from;

	/**
	 * 
	 * @var string
	 */
	protected $sender;

	/**
	 * 
	 * @var string
	 */
	protected $subject;

	/**
	 * 
	 * @var string
	 */
	protected $text;

	/**
	 * 
	 * @var string
	 */
	protected $html;

	/**
	 * 
	 * @var array
	 */
	protected $attachments = array();

	/**
	 * 
	 * @var string
	 */
	public $protocol = 'mail';

	/**
	 * 
	 * @var string
	 */
	public $newline = "\n";

	/**
	 * 
	 * @var string
	 */
	public $crlf = "\r\n";

	/**
	 * 
	 * @var bool
	 */
	public $verp = false;


	/**
	 * 
	 * @var string
	 */
	protected $parameter = '';

	/**
	 * 
	 * @var \Phacil\Framework\Config
	 */
	private $config;

	/**
	 * 
	 * @var \Phacil\Framework\Mail\Api\DriverInterface
	 */
	private $driver;

	private $replyTo;

	/**
	 * @param \Phacil\Framework\Config $config 
	 * @param \Phacil\Framework\Registry $registry 
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	public function __construct(
		Config $config,
		\Phacil\Framework\Registry $registry
	) {
		$this->config = $config;

		$this->protocol = $this->config->get('config_mail_protocol') ?: $this->protocol;

		if (!\Phacil\Framework\Registry::checkPreferenceExist(\Phacil\Framework\Mail\Api\DriverInterface::class)) {
			if($this->protocol == self::PROTOCOL_SMTP) {
				\Phacil\Framework\Registry::addDIPreference(\Phacil\Framework\Mail\Api\DriverInterface::class, \Phacil\Framework\Mail\Drivers\SMTP::class);
			}
			if($this->protocol == self::PROTOCOL_MAIL) {
				\Phacil\Framework\Registry::addDIPreference(\Phacil\Framework\Mail\Api\DriverInterface::class, \Phacil\Framework\Mail\Drivers\Native::class);
			}
		}

		/** @var \Phacil\Framework\Mail\Api\DriverInterface */
		$this->driver = $registry->getInstance(\Phacil\Framework\Mail\Api\DriverInterface::class, [$this]);
	}

	/** @inheritdoc */
	public function getVerp(){
		return $this->verp;
	}

	/** @inheritdoc */
	public function getFrom() {
		return $this->from;
	}

	public function getParameter()
	{
		return $this->parameter;
	}

	/** @inheritdoc */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setTo($to) {
		$this->to = $to;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFrom($from) {
		$this->from = $from;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setSender($sender) {
		$this->sender = html_entity_decode($sender, ENT_QUOTES, 'UTF-8');
		return $this;
	}

	/** @inheritdoc  */
	public function getSender() {
		return $this->sender ?: $this->from;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setSubject($subject) {
		$this->subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
		return $this;
	}

	/** @inheritdoc  */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setText($text) {
		$this->text = $text;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setHtml($html) {
		$this->html = $html;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setReplyTo($replyTo) {
		$this->replyTo = $replyTo;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getReplyTo() {
		return $this->replyTo;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addAttachment($file, $filename = '') {
		if (!$filename) {
			$filename = basename($file);
		}
				
		$this->attachments[] = [
			'filename' => $filename,
			'file'     => $file
		];

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	protected function validade() {
		if (!$this->to || !is_string($this->to)) {
			throw new \Phacil\Framework\Exception\InvalidArgumentException('A valid e-mail To is required!');
		}

		if (!$this->from || !is_string($this->from)) {
			throw new \Phacil\Framework\Exception\InvalidArgumentException('Valid e-mail From required!');
		}

		if (empty($this->getSender()) || !is_string($this->getSender())) {
			throw new \Phacil\Framework\Exception\InvalidArgumentException('A valid email sender is required!');
		}

		if (!$this->subject || !is_string($this->subject)) {
			throw new \Phacil\Framework\Exception\InvalidArgumentException('A valid email subject is required!');
		}

		if ((!$this->text) && (!$this->html)) {
			throw new \Phacil\Framework\Exception\InvalidArgumentException('E-Mail message required!');
		}
	}

	/** @inheritdoc */
	public function send() {
		$this->validade();

		if (is_array($this->to)) {
			$to = implode(',', $this->to);
		} else {
			$to = $this->to;
		}

		$boundary = '----=_NextPart_' . md5((string)time());

		$header  = 'MIME-Version: 1.0' . $this->newline;

		if ($this->protocol != self::PROTOCOL_MAIL) {
			$header .= 'To: <' . $to . '>' . $this->newline;
			$header .= 'Subject: =?UTF-8?B?' . base64_encode($this->getSubject()) . '?=' . $this->newline;
		}

		$header .= 'Date: ' . date('D, d M Y H:i:s O') . $this->newline;
		$header .= 'From: =?UTF-8?B?' . base64_encode($this->getSender()) . '?= <' . $this->getFrom() . '>' . $this->newline;

		if (empty($this->getReplyTo())) {
			$header .= 'Reply-To: =?UTF-8?B?' . base64_encode($this->getSender()) . '?= <' . $this->getFrom() . '>' . $this->newline;
		} else {
			$header .= 'Reply-To: =?UTF-8?B?' . base64_encode($this->getReplyTo()) . '?= <' . $this->getReplyTo() . '>' . $this->newline;
		}

		$header .= 'Return-Path: ' . $this->getFrom() . $this->newline;
		$header .= 'X-Mailer: ' . self::XMAILER_SIGN . ' with PHP/' . (defined('PHP_MAJOR_VERSION') ? PHP_MAJOR_VERSION : phpversion()) . $this->newline;
		$header .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . $this->newline . $this->newline;

		$message = '--' . $boundary . $this->newline;

		if (empty($this->getHtml())) {
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: base64' . $this->newline . $this->newline;
			$message .= chunk_split(base64_encode($this->getText()), 950) . $this->newline;
		} else {
			$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . $this->newline . $this->newline;
			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: base64' . $this->newline . $this->newline;

			if (!empty($this->getText())) {
				$message .= chunk_split(base64_encode($this->getText()), 950) . $this->newline;
			} else {
				$message .= chunk_split(base64_encode('This is a HTML email and your email client software does not support HTML email!'), 950) . $this->newline;
			}

			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/html; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: base64' . $this->newline . $this->newline;
			$message .= chunk_split(base64_encode($this->getHtml()), 950) . $this->newline;
			$message .= '--' . $boundary . '_alt--' . $this->newline;
		}

		if (!empty($this->getAttachments())) {
			foreach ($this->getAttachments() as $attachment) {
				if (is_file($attachment['file'])) {
					$handle = fopen($attachment['file'], 'r');

					$content = fread($handle, filesize($attachment['file']));

					fclose($handle);

					$message .= '--' . $boundary . $this->newline;
					$message .= 'Content-Type: application/octet-stream; name="' . basename($attachment['file']) . '"' . $this->newline;
					$message .= 'Content-Transfer-Encoding: base64' . $this->newline;
					$message .= 'Content-Disposition: attachment; filename="' . basename($attachment['filename']) . '"' . $this->newline;
					$message .= 'Content-ID: <' . urlencode(basename($attachment['filename'])) . '>' . $this->newline;
					$message .= 'X-Attachment-Id: ' . urlencode(basename($attachment['filename'])) . $this->newline . $this->newline;
					$message .= chunk_split(base64_encode($content), 950);
				}
			}
		}

		$message .= '--' . $boundary . '--' . $this->newline;
		
		return $this->driver->send($to, $message, $header);
	}
}
