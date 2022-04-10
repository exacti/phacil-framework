<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
final class Mail {

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
	public $hostname;

	/**
	 * 
	 * @var string
	 */
	public $username;

	/**
	 * 
	 * @var string
	 */
	public $password;

	/**
	 * 
	 * @var int
	 */
	public $port = 25;

	/**
	 * 
	 * @var int
	 */
	public $timeout = 5;

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
	public $parameter = '';

	/**
	 * @param string $to 
	 * @return void 
	 */
	public function setTo($to) {
		$this->to = $to;
	}

	/**
	 * 
	 * @param string $from 
	 * @return void 
	 */
	public function setFrom($from) {
		$this->from = $from;
	}

	/**
	 * @param string $sender 
	 * @return void 
	 */
	public function setSender($sender) {
		$this->sender = html_entity_decode($sender, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * @param string $subject 
	 * @return void 
	 */
	public function setSubject($subject) {
		$this->subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * @param string $text 
	 * @return void 
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * @param string $html 
	 * @return void 
	 */
	public function setHtml($html) {
		$this->html = $html;
	}

	/**
	 * @param string $file 
	 * @param string $filename 
	 * @return void 
	 */
	public function addAttachment($file, $filename = '') {
		if (!$filename) {
			$filename = basename($file);
		}
				
		$this->attachments[] = [
			'filename' => $filename,
			'file'     => $file
		];
	}

	/** @return void  */
	public function send() {
		if (!$this->to) {
			throw new \Phacil\Framework\Exception('Error: E-Mail to required!');
		}

		if (!$this->from) {
			throw new \Phacil\Framework\Exception('Error: E-Mail from required!');
		}

		if (!$this->sender) {
			throw new \Phacil\Framework\Exception('Error: E-Mail sender required!');
		}

		if (!$this->subject) {
			throw new \Phacil\Framework\Exception('Error: E-Mail subject required!');
		}

		if ((!$this->text) && (!$this->html)) {
			throw new \Phacil\Framework\Exception('Error: E-Mail message required!');
		}

		if (is_array($this->to)) {
			$to = implode(',', $this->to);
		} else {
			$to = $this->to;
		}

		$boundary = '----=_NextPart_' . md5(time());

		$header = '';
		
		$header .= 'MIME-Version: 1.0' . $this->newline;
		
		if ($this->protocol != 'mail') {
			$header .= 'To: ' . $to . $this->newline;
			$header .= 'Subject: ' . $this->subject . $this->newline;
		}
		
		$header .= 'Date: ' . date("D, d M Y H:i:s O") . $this->newline;
		$header .= 'From: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;
		$header .= 'Reply-To: ' . $this->sender . '<' . $this->from . '>' . $this->newline;
		$header .= 'Return-Path: ' . $this->from . $this->newline;
		$header .= 'X-Mailer: PHP/' . phpversion() . $this->newline;
		$header .= 'Content-Type: multipart/related; boundary="' . $boundary . '"' . $this->newline;

		if (!$this->html) {
			$message  = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->text . $this->newline;
		} else {
			$message  = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . $this->newline . $this->newline;
			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;

			if ($this->text) {
				$message .= $this->text . $this->newline;
			} else {
				$message .= 'This is a HTML email and your email client software does not support HTML email!' . $this->newline;
			}

			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/html; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->html . $this->newline;
			$message .= '--' . $boundary . '_alt--' . $this->newline;
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment['file'])) {
				$handle = fopen($attachment['file'], 'r');
				
				$content = fread($handle, filesize($attachment['file']));
				
				fclose($handle);

				$message .= '--' . $boundary . $this->newline;
				$message .= 'Content-Type: application/octetstream; name="' . basename($attachment['file']) . '"' . $this->newline;
				$message .= 'Content-Transfer-Encoding: base64' . $this->newline;
				$message .= 'Content-Disposition: attachment; filename="' . basename($attachment['filename']) . '"' . $this->newline;
				$message .= 'Content-ID: <' . basename($attachment['filename']) . '>' . $this->newline;
				$message .= 'X-Attachment-Id: ' . basename($attachment['filename']) . $this->newline . $this->newline;
				$message .= chunk_split(base64_encode($content));
			}
		}

		$message .= '--' . $boundary . '--' . $this->newline;

		if ($this->protocol == 'mail') {
			ini_set('sendmail_from', $this->from);

			if ($this->parameter) {
				mail($to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header, $this->parameter);
			} else {
				mail($to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header);
			}

		} elseif ($this->protocol == 'smtp') {
			$handle = fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);

			if (!$handle) {
				throw new \Phacil\Framework\Exception('Error: ' . $errstr . ' (' . $errno . ')');
			} else {
				if (substr(PHP_OS, 0, 3) != 'WIN') {
					socket_set_timeout($handle, $this->timeout, 0);
				}

				while ($line = fgets($handle, 515)) {
					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($this->hostname, 0, 3) == 'tls') {
					fputs($handle, 'STARTTLS' . $this->crlf);

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 220) {
						throw new \Phacil\Framework\Exception('Error: STARTTLS not accepted from server!');
					}
				}

				if (!empty($this->username)  && !empty($this->password)) {
					fputs($handle, 'EHLO ' . getenv('SERVER_NAME') . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 250) {
						throw new \Phacil\Framework\Exception('Error: EHLO not accepted from server!');
					}

					fputs($handle, 'AUTH LOGIN' . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 334) {
						throw new \Phacil\Framework\Exception('Error: AUTH LOGIN not accepted from server!');
					}

					fputs($handle, base64_encode($this->username) . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 334) {
						throw new \Phacil\Framework\Exception('Error: Username not accepted from server!');
					}

					fputs($handle, base64_encode($this->password) . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 235) {
						throw new \Phacil\Framework\Exception('Error: Password not accepted from server!');
					}
				} else {
					fputs($handle, 'HELO ' . getenv('SERVER_NAME') . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 250) {
						throw new \Phacil\Framework\Exception('Error: HELO not accepted from server!');
					}
				}

				if ($this->verp) {
					fputs($handle, 'MAIL FROM: <' . $this->from . '>XVERP' . $this->crlf);
				} else {
					fputs($handle, 'MAIL FROM: <' . $this->from . '>' . $this->crlf);
				}

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 250) {
					throw new \Phacil\Framework\Exception('Error: MAIL FROM not accepted from server!');
				}

				if (!is_array($this->to)) {
					fputs($handle, 'RCPT TO: <' . $this->to . '>' . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
						throw new \Phacil\Framework\Exception('Error: RCPT TO not accepted from server!');
					}
				} else {
					foreach ($this->to as $recipient) {
						fputs($handle, 'RCPT TO: <' . $recipient . '>' . $this->crlf);

						$reply = '';

						while ($line = fgets($handle, 515)) {
							$reply .= $line;

							if (substr($line, 3, 1) == ' ') {
								break;
							}
						}

						if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
							throw new \Phacil\Framework\Exception('Error: RCPT TO not accepted from server!');
						}
					}
				}

				fputs($handle, 'DATA' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 354) {
					throw new \Phacil\Framework\Exception('Error: DATA not accepted from server!');
				}
            	
				// According to rfc 821 we should not send more than 1000 including the CRLF
				$message = str_replace("\r\n", "\n",  $header . $message);
				$message = str_replace("\r", "\n", $message);
				
				$lines = explode("\n", $message);
				
				foreach ($lines as $line) {
					$results = str_split($line, 998);
					
					foreach ($results as $result) {
						if (substr(PHP_OS, 0, 3) != 'WIN') {
							fputs($handle, $result . $this->crlf);
						} else {
							fputs($handle, str_replace("\n", "\r\n", $result) . $this->crlf);
						}							
					}
				}
				
				fputs($handle, '.' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 250) {
					throw new \Phacil\Framework\Exception('Error: DATA not accepted from server!');
				}
				
				fputs($handle, 'QUIT' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 221) {
					throw new \Phacil\Framework\Exception('Error: QUIT not accepted from server!');
				}

				fclose($handle);
			}
		}
	}
}
