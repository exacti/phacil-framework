<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Api\BuilderInterface;
use Phacil\Framework\MagiQL\Manipulation\Select;
use Phacil\Framework\MagiQL\Syntax\Column;
use Phacil\Framework\MagiQL\Syntax\SyntaxFactory;
use Phacil\Framework\Databases\Api\DriverInterface as DatabaseDriverInterface;

/**
 * Class ColumnWriter.
 */
class ColumnWriter
{
    /**
     * @var BuilderInterface
     */
    protected $writer;

    private $writerAlternative;

    /**
     * @var PlaceholderWriter
     */
    protected $placeholderWriter;

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface    $writer
     * @param PlaceholderWriter $placeholderWriter
     */
    public function __construct(BuilderInterface $writer, PlaceholderWriter $placeholderWriter)
    {
        $this->writer = $writer;
        $this->placeholderWriter = $placeholderWriter;
    }

    /**
     * @param Select $select
     *
     * @return array
     */
    public function writeSelectsAsColumns(Select $select)
    {
        $selectAsColumns = $select->getColumnSelects();

        if (!empty($selectAsColumns)) {
            $selectWriter = WriterFactory::createSelectWriter($this->writer, $this->placeholderWriter);
            $selectAsColumns = $this->selectColumnToQuery($selectAsColumns, $selectWriter);
        }

        return $selectAsColumns;
    }

    /**
     * @param array        $selectAsColumns
     * @param SelectWriter $selectWriter
     *
     * @return mixed
     */
    protected function selectColumnToQuery(array &$selectAsColumns, SelectWriter $selectWriter)
    {
        \array_walk(
            $selectAsColumns,
            function (&$column) use (&$selectWriter) {
                $keys = \array_keys($column);
                $key = \array_pop($keys);

                $values = \array_values($column);
                $value = $values[0];

                if (\is_numeric($key)) {
                    /* @var Column $value */
                    $key = $this->getWriteObj()->writeTableName($value->getTable());
                }
                $column = $selectWriter->selectToColumn($key, $value);
            }
        );

        return $selectAsColumns;
    }

    /**
     * @param Select $select
     *
     * @return array
     */
    public function writeValueAsColumns(Select $select)
    {
        $valueAsColumns = $select->getColumnValues();
        $newColumns = [];

        if (!empty($valueAsColumns)) {
            foreach ($valueAsColumns as $alias => $value) {
                $value = $this->writer->writePlaceholderValue($value);
                $newValueColumn = array($alias => $value);

                $newColumns[] = SyntaxFactory::createColumn($newValueColumn, null);
            }
        }

        return $newColumns;
    }

    /**
     * @param Select $select
     *
     * @return array
     */
    public function writeFuncAsColumns(Select $select)
    {
        $funcAsColumns = $select->getColumnFuncs();
        $newColumns = [];

        if (!empty($funcAsColumns)) {
            foreach ($funcAsColumns as $alias => $value) {
                $funcName = $value['func'];
                $funcArgs = (!empty($value['args'])) ? '('.implode(', ', $value['args']).')' : '';

                $newFuncColumn = array($alias => $funcName.$funcArgs);
                $newColumns[] = SyntaxFactory::createColumn($newFuncColumn, null);
            }
        }

        return $newColumns;
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public function writeColumnWithAlias(Column $column)
    {
        if (($alias = $column->getAlias()) && !$column->isAll()) {
            return $this->writeColumn($column).' AS '.$this->getWriteObj()->writeColumnAlias($alias);
        }

        return $this->writeColumn($column);
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public function writeColumn(Column $column)
    {
        $alias = $column->getTable()->getAlias();
        $table = ($alias) ? $this->getWriteObj()->writeTableAlias($alias) : $this->getWriteObj()->writeTable($column->getTable());

        $columnString = (empty($table)) ? '' : "{$table}.";
        $columnString .= $this->getWriteObj()->writeColumnName($column);

        return $columnString;
    }

    /**
     * 
     * @return \Phacil\Framework\MagiQL\Api\BuilderInterface 
     */
    protected function getWriteObj()
    {
        if($this->writerAlternative) return $this->writerAlternative;

        $writerDbType = null;
        if (method_exists($this->writer, 'getDb')) {
            /** @var \Phacil\Framework\MagiQL */
            $writer = $this->writer;
            $writerDbType = $writer->getDb()->getDBTypeId();
            unset($writer);
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
                $this->writerAlternative = $this->writer;
                break;
        }

        return $this->writerAlternative;
    }
}
