<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 namespace Phacil\Framework\Session\Handlers;

use Phacil\Framework\Api\Database as FrameworkDatabase;
use Phacil\Framework\Encryption;

/**
 * Data base session save handler
 */
class Database implements \Phacil\Framework\Session\Api\HandlerInterface
{
	const TABLE_NAME = 'session';

	const COLUMN_DATA = 'session_data';

	const COLUMN_ID = 'session_id';

	const COLUMN_EXPIRES = 'session_expires';

	/**
	 * Session data table name
	 *
	 * @var string
	 */
	protected $_sessionTable;

	/**
	 * Database write connection
	 * 
	 * @var \Phacil\Framework\Databases\Object\ResultInterface|\Phacil\Framework\Database::Cache|\Phacil\Framework\MagiQL
	 */
	protected $connection;

	/**
	 * 
	 * @var \Phacil\Framework\Encryption
	 */
	private $encryptor;

	
	public function __construct(
		FrameworkDatabase $resource,
		Encryption $encryptor
	) {
		$this->_sessionTable = self::TABLE_NAME;
		$this->connection = $resource->query();
		$this->checkConnection();
		$this->encryptor = $encryptor;
	}

	public function getFailedLockAttempts() { }

	public function setName($name) { }

	private function hashed($hash) {
		//return $this->encryptor->hash($hash);
		return $hash;
	}

	/**
	 * Check DB connection
	 *
	 * @return void
	 * @throws \Magento\Framework\Exception\SessionException
	 */
	protected function checkConnection()
	{
		if (!$this->connection) {
			throw new \Phacil\Framework\Exception(
				"The write connection to the database isn't available. Please try again later."
			);
		}
		if (!$this->connection->isTableExists($this->_sessionTable)) {
			throw new \Phacil\Framework\Exception(
				"The database storage table doesn't exist. Verify the table and try again."
			);
		}
	}

	/**
	 * Open session
	 *
	 * @param string $savePath ignored
	 * @param string $sessionName ignored
	 * @return bool
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	#[\ReturnTypeWillChange]
	public function open($savePath, $sessionName)
	{
		return true;
	}

	/**
	 * Close session
	 *
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function close()
	{
		return true;
	}

	/**
	 * Fetch session data
	 *
	 * @param string $sessionId
	 * @return string
	 */
	#[\ReturnTypeWillChange]
	public function read($sessionId)
	{
		// need to use write connection to get the most fresh DB sessions
		$select = $this->connection->select()->from(
			$this->_sessionTable
		);
		$select->where()->eq(self::COLUMN_ID, $this->hashed($sessionId))->end();
		
		$data = $select->load();

		if($data->getNumRows() < 1){
			return null;
		}

		$data = $data->getRow()->getValue(self::COLUMN_DATA);

		// check if session data is a base64 encoded string
		$decodedData = base64_decode($data, true);
		if ($decodedData !== false) {
			$data = $decodedData;
		}
		return $data;
	}

	/**
	 * Update session
	 *
	 * @param string $sessionId
	 * @param string $sessionData
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function write($sessionId, $sessionData)
	{
		$hashedSessionId = $this->hashed($sessionId);
		$select = $this->connection->select()->from($this->_sessionTable);
		$select->where()->eq(self::COLUMN_ID, $hashedSessionId)->end();
		$exists = $select->load();

		// encode session serialized data to prevent insertion of incorrect symbols
		$sessionData = base64_encode($sessionData);
		$bind = [self::COLUMN_EXPIRES => time(), self::COLUMN_DATA => $sessionData];

		if ($exists->getNumRows() > 0) {
			$update = $this->connection->update($this->_sessionTable, $bind);
			$update->where()->eq(self::COLUMN_ID, $hashedSessionId)->end();
			$update->load();
		} else {
			$bind[self::COLUMN_ID] = $hashedSessionId;
			$this->connection->insert($this->_sessionTable, $bind)->load();
		}
		return true;
	}

	/**
	 * Destroy session
	 *
	 * @param string $sessionId
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function destroy($sessionId)
	{
		$del = $this->connection->delete($this->_sessionTable);
		$del->where()->eq(self::COLUMN_ID, $this->hashed($sessionId))->end();
		return $del->load();
	}

	/**
	 * Garbage collection
	 *
	 * @param int $maxLifeTime
	 * @return bool
	 * @SuppressWarnings(PHPMD.ShortMethodName)
	 */
	#[\ReturnTypeWillChange]
	public function gc($maxLifeTime)
	{
		$del = $this->connection->delete($this->_sessionTable);
		$del->where()->lessThan(self::COLUMN_EXPIRES, time() - $maxLifeTime);
		return $del->load();
	}
}
