<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\Session\Handlers;

/**
 * File session handler
 *
 * @since 2.0.0
 * @package Phacil\Framework\Session;
 */
class File implements \Phacil\Framework\Session\Api\HandlerInterface
{
	const KEY_EXPIRES = 'session_expires';

	const SHORT_NAME = 'file';

	/**
	 * 
	 * @var \Phacil\Framework\Config
	 */
	private $config;

	/**
	 * 
	 * @var \Phacil\Framework\Encryption
	 */
	private $encryptor;

	/**
	 * 
	 * @var \Phacil\Framework\Json
	 */
	private $json;

	/**
	 * 
	 * @var bool
	 */
	protected $isFirstSession = true;

	/**
	 * Constructor
	 *
	 * @param \Phacil\Framework\Config $config
	 */
	public function __construct(
		\Phacil\Framework\Config $config, 
		\Phacil\Framework\Encryption $encryption, 
		\Phacil\Framework\Json $json
	){
		$this->config = $config;
		$this->encryptor = $encryption;
		$this->json = $json;
		if(!\Phacil\Framework\Config::DIR_SESSION())
			\Phacil\Framework\Config::DIR_SESSION(\Phacil\Framework\Config::DIR_CACHE()."sessions/");

		$this->checkDir();
	}

	/**
	 * @return void 
	 * @throws \Phacil\Framework\Exception 
	 */
	protected function checkDir()
	{
		if (!is_dir(\Phacil\Framework\Config::DIR_SESSION())) {
			mkdir(\Phacil\Framework\Config::DIR_SESSION(), 0764, true);
		}
		if (!is_writable(\Phacil\Framework\Config::DIR_SESSION())) {
			throw new \Phacil\Framework\Exception(
				"The session dir storage doesn't exist or isn't writable. Verify the permissions and try again."
			);
		}
	}

	/** {@inheritdoc} */
	public function getFailedLockAttempts() { }

	/** {@inheritdoc} */
	public function setName($name) { }

	/** @return int  */
	protected function getLifetime()
	{
		return (int)$this->config->get('session_expire') ?: self::DEFAULT_SESSION_LIFETIME;
	}

	/** @return int  */
	protected function getFirstLifetime()
	{
		return (int)$this->config->get('session_first_lifetime') ?: self::DEFAULT_SESSION_FIRST_LIFETIME;
	}

	/**
	 * @param string $hash 
	 * @return string|false 
	 */
	private function hashed($hash)
	{
		//$this->encryptor->setHashAlgo('sha256');
		return $this->encryptor->hash($hash) ?: $hash;
		//return $hash;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\ReturnTypeWillChange]
	public function close()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\ReturnTypeWillChange]
	public function open($savePath, $sessionName)
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\ReturnTypeWillChange]
	public function read($session_id)
	{
		$file = \Phacil\Framework\Config::DIR_SESSION() . 'sess_' . $this->hashed(basename($session_id));

		if (is_file($file)) {
			$data = $this->json->decode(file_get_contents($file));
			if($data && $data[self::KEY_EXPIRES] >= time()){
				$this->isFirstSession = false;
				return $data['data'];
			} else {
				$this->destroy($session_id);
			}
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\ReturnTypeWillChange]
	public function write($session_id, $data)
	{
		$data = [
			'data' => $data,
			self::KEY_EXPIRES => time() + ($this->isFirstSession ? $this->getFirstLifetime() : $this->getLifetime())
		];
		file_put_contents(\Phacil\Framework\Config::DIR_SESSION() . 'sess_' . $this->hashed(basename($session_id)), $this->json->encode($data));

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\ReturnTypeWillChange]
	public function destroy($session_id)
	{
		$file = \Phacil\Framework\Config::DIR_SESSION() . 'sess_' . $this->hashed(basename($session_id));

		if (is_file($file) && is_writable($file)) {
			return unlink($file);
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\ReturnTypeWillChange]
	public function gc($maxLifeTime)
	{
		if (round(mt_rand(1, $this->config->get('session_divisor') / $this->config->get('session_probability'))) == 1) {
			$expire = time() - $this->config->get('session_expire');

			$files = scandir(\Phacil\Framework\Config::DIR_SESSION());

			foreach ($files as $file) {
				if (is_file($file) && filemtime($file) < $expire && is_writable($file)) {
					unlink($file);
				}
			}
		}

		return true;
	}
}
