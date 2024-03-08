<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */


namespace Phacil\Framework\MagiQL\Builder;

use Phacil\Framework\MagiQL\Builder as MagiQLBuilder;
use Phacil\Framework\Databases\Api\DriverInterface as DatabaseDriverInterface;
use Phacil\Framework\MagiQL\Syntax\Column;
use Phacil\Framework\MagiQL\Syntax\Table;

abstract class AbstractBuilder extends MagiQLBuilder {

	private $writerAlternative;

	/**
	 * {@inheritdoc}
	 */
	public function writeColumnName(Column $column)
	{
		return $this->getWriteObj() ? $this->getWriteObj()->writeColumnName($column) : parent::writeColumnName($column);
	}

	/**
	 * {@inheritdoc}
	 */
	public function writeTableName(Table $table)
	{
		return $this->getWriteObj() ? $this->getWriteObj()->writeTableName($table) : parent::writeTableName($table);
	}

	/**
	 * {@inheritdoc}
	 */
	public function writeTableAlias($alias)
	{
		return $this->getWriteObj() ? $this->getWriteObj()->writeTableAlias($alias) : parent::writeTableAlias($alias);
	}

	/**
	 * {@inheritdoc}
	 */
	public function writeColumnAlias($alias)
	{
		return $this->getWriteObj() ? $this->getWriteObj()->writeColumnAlias($alias) : parent::writeColumnAlias($alias);
	}

	/**
	 * 
	 * @return \Phacil\Framework\MagiQL\Api\BuilderInterface 
	 */
	protected function getWriteObj()
	{
		if ($this->writerAlternative) return $this->writerAlternative;

		$writerDbType = null;
		if (method_exists($this, 'getDb')) {
			$writerDbType = $this->getDb()->getDBTypeId();
		}
		switch ($writerDbType) {
			case DatabaseDriverInterface::LIST_DB_TYPE_ID['MYSQL']:
				$this->writerAlternative = new \Phacil\Framework\MagiQL\Builder\Syntax\Adapt\MySQL\ColumnTable();
				break;

			case DatabaseDriverInterface::LIST_DB_TYPE_ID['MSSQL']:
				$this->writerAlternative = new \Phacil\Framework\MagiQL\Builder\Syntax\Adapt\MSSQL\ColumnTable();
				break;

			case DatabaseDriverInterface::LIST_DB_TYPE_ID['POSTGRE']:
			case DatabaseDriverInterface::LIST_DB_TYPE_ID['SQLLITE3']:
				$this->writerAlternative = new \Phacil\Framework\MagiQL\Builder\Syntax\Adapt\PostgreSQL\ColumnTable();
				break;

			case DatabaseDriverInterface::LIST_DB_TYPE_ID['ORACLE']:
				$this->writerAlternative = new \Phacil\Framework\MagiQL\Builder\Syntax\Adapt\Oracle\ColumnTable();
				break;

			default:
				return false;
				break;
		}

		return $this->writerAlternative;
	}
}