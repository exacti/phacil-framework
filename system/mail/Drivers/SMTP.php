<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Mail\Drivers;

class SMTP implements \Phacil\Framework\Mail\Api\DriverInterface {
	/**
	 * 
	 * @var string
	 */
	protected $hostname;

	/**
	 * 
	 * @var string
	 */
	protected $username;

	/**
	 * 
	 * @var string
	 */
	protected $password;

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

	private $crlf = self::CRLF;

	private $newline = self::NEWLINE;

	private $from;

	private $verp;

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
		$this->verp = $this->mail->getVerp();
		$this->from = $this->mail->getFrom();
		$this->port = $this->mail->getConfig()->get(self::CONFIG_SMTP_PORT) ?: $this->port;
		$this->hostname = $this->mail->getConfig()->get(self::CONFIG_SMTP_HOSTNAME);
		$this->username = $this->mail->getConfig()->get(self::CONFIG_SMTP_USERNAME);
		$this->password = $this->mail->getConfig()->get(self::CONFIG_SMTP_PASSWORD);
		$this->timeout = $this->mail->getConfig()->get(self::CONFIG_SMTP_TIMEOUT) ?: $this->timeout;

		if(!$this->hostname || !is_string($this->hostname)) 
			throw new \Phacil\Framework\Exception\InvalidArgumentException('STMP requires a valid hostname configuration');
		
		if(!empty($this->username) && !is_string($this->username)) 
			throw new \Phacil\Framework\Exception\InvalidArgumentException('STMP requires a valid smtp username configuration');
		
		if(!empty($this->password) && !is_string($this->password)) 
			throw new \Phacil\Framework\Exception\InvalidArgumentException('STMP requires a valid smtp password configuration');
		
		if(!$this->port || !is_int($this->port)) 
			throw new \Phacil\Framework\Exception\InvalidArgumentException('STMP requires a valid smtp port configuration');
		
		if(!$this->timeout || !is_int($this->timeout)) 
			throw new \Phacil\Framework\Exception\InvalidArgumentException('STMP requires a valid timeout configuration');
	}

	/**
	 * {@inheritdoc}
	 */
	public function send($to, $message, $header){
		$this->validate();

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
				$reply = '';

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

			if (!is_array($to)) {
				fputs($handle, 'RCPT TO: <' . $to . '>' . $this->crlf);

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
				foreach ($to as $recipient) {
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

			return true;
		}
	
	}

}