<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL;

use Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory;
use Phacil\Framework\MagiQL\Manipulation\AbstractBaseQuery;
use Phacil\Framework\MagiQL\Api\QueryInterface;
use Phacil\Framework\MagiQL\Manipulation\QueryFactory;
use Phacil\Framework\MagiQL\Manipulation\Select;
use Phacil\Framework\MagiQL\Syntax\Column;
use Phacil\Framework\MagiQL\Syntax\Table;

/**
 * Class Generic.
 */
class Builder implements \Phacil\Framework\MagiQL\Api\BuilderInterface
{
    /**
     * The placeholder parameter bag.
     *
     * @var \Phacil\Framework\MagiQL\Builder\Syntax\PlaceholderWriter
     */
    protected $placeholderWriter;

    /**
     * The Where writer.
     *
     * @var \Phacil\Framework\MagiQL\Builder\Syntax\WhereWriter
     */
    protected $whereWriter;

    /**
     * Array holding the writers for each query part. Methods are called upon request and stored in
     * the $queryWriterInstances array.
     *
     * @var array
     */
    protected $queryWriterArray = [
        'SELECT' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createSelectWriter',
        'INSERT' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createInsertWriter',
        'UPDATE' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createUpdateWriter',
        'DELETE' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createDeleteWriter',
        'INTERSECT' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createIntersectWriter',
        'MINUS' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createMinusWriter',
        'UNION' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createUnionWriter',
        'UNION ALL' => '\Phacil\Framework\MagiQL\Builder\Syntax\WriterFactory::createUnionAllWriter',
    ];

    /**
     * Array that stores instances of query writers.
     *
     * @var array
     */
    protected $queryWriterInstances = [
        'SELECT' => null,
        'INSERT' => null,
        'UPDATE' => null,
        'DELETE' => null,
        'INTERSECT' => null,
        'MINUS' => null,
        'UNION' => null,
        'UNION ALL' => null,
    ];

    /**
     * Creates writers.
     */
    public function __construct()
    {
        $this->placeholderWriter = WriterFactory::createPlaceholderWriter();
    }

    /**
     * @param string $table
     * @param array  $columns
     *
     * @return \Phacil\Framework\MagiQL\Manipulation\Select
     */
    public function select($table = null, array $columns = null)
    {
        return $this->injectBuilder(QueryFactory::createSelect($table, $columns));
    }

    /**
     * @param \Phacil\Framework\MagiQL\Manipulation\AbstractBaseQuery
     *
     * @return \Phacil\Framework\MagiQL\Manipulation\AbstractBaseQuery
     */
    protected function injectBuilder(AbstractBaseQuery $query)
    {
        return $query->setBuilder($this);
    }

    /**
     * @param string $table
     * @param array  $values
     *
     *@return AbstractBaseQuery
     */
    public function insert($table = null, array $values = null)
    {
        return $this->injectBuilder(QueryFactory::createInsert($table, $values));
    }

    /**
     * @param string $table
     * @param array  $values
     *
     *@return AbstractBaseQuery
     */
    public function update($table = null, array $values = null)
    {
        return $this->injectBuilder(QueryFactory::createUpdate($table, $values));
    }

    /**
     * @param string $table
     *
     * @return \Phacil\Framework\MagiQL\Manipulation\Delete
     */
    public function delete($table = null)
    {
        return $this->injectBuilder(QueryFactory::createDelete($table));
    }

    /**
     * @return \Phacil\Framework\MagiQL\Manipulation\Intersect
     */
    public function intersect()
    {
        return QueryFactory::createIntersect();
    }

    /**
     * @return \Phacil\Framework\MagiQL\Manipulation\Union
     */
    public function union()
    {
        return QueryFactory::createUnion();
    }

    /**
     * @return \Phacil\Framework\MagiQL\Manipulation\UnionAll
     */
    public function unionAll()
    {
        return QueryFactory::createUnionAll();
    }

    /**
     * @param \Phacil\Framework\MagiQL\Manipulation\Select $first
     * @param \Phacil\Framework\MagiQL\Manipulation\Select $second
     *
     * @return \Phacil\Framework\MagiQL\Manipulation\Minus
     */
    public function minus(Select $first, Select $second)
    {
        return QueryFactory::createMinus($first, $second);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->placeholderWriter->get();
    }

    /**
     * @param QueryInterface $query
     * @param bool           $resetPlaceholders
     *
     * @return string
     *
     * @throws \Phacil\Framework\MagiQL\Builder\BuilderException
     */
    public function write(QueryInterface $query, $resetPlaceholders = true)
    {
        if ($resetPlaceholders) {
            $this->placeholderWriter->reset();
        }

        $queryPart = $query->partName();

        if (false === empty($this->queryWriterArray[$queryPart])) {
            $this->createQueryObject($queryPart);

            return $this->queryWriterInstances[$queryPart]->write($query);
        }

        throw new \Phacil\Framework\MagiQL\Builder\BuilderException('Query builder part not defined.');
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeJoin(Select $select)
    {
        if (null === $this->whereWriter) {
            $this->whereWriter = WriterFactory::createWhereWriter($this, $this->placeholderWriter);
        }

        $sql = ($select->getJoinType()) ? "{$select->getJoinType()} " : '';
        $sql .= 'JOIN ';
        $sql .= $this->writeTableWithAlias($select->getTable());
        $sql .= ' ON ';
        $sql .= $this->whereWriter->writeWhere($select->getJoinCondition());

        return $sql;
    }

    /**
     * @param Table $table
     *
     * @return string
     */
    public function writeTableWithAlias(Table $table)
    {
        $alias = ($table->getAlias()) ? " AS {$this->writeTableAlias($table->getAlias())}" : '';
        $schema = ($table->getSchema()) ? "{$table->getSchema()}." : '';

        return $schema.$this->writeTableName($table).$alias;
    }

    /**
     * @param $alias
     *
     * @return mixed
     */
    public function writeTableAlias($alias)
    {
        return $alias;
    }

    /**
     * Returns the table name.
     *
     * @param Table $table
     *
     * @return string
     */
    public function writeTableName(Table $table)
    {
        return $table->getName();
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    public function writeColumnAlias($alias)
    {
        return sprintf('"%s"', $alias);
    }

    /**
     * @param Table $table
     *
     * @return string
     */
    public function writeTable(Table $table)
    {
        $schema = ($table->getSchema()) ? "{$table->getSchema()}." : '';

        return $schema.$this->writeTableName($table);
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function writeValues(array &$values)
    {
        \array_walk(
            $values,
            function (&$value) {
                $value = $this->writePlaceholderValue($value);
            }
        );

        return $values;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function writePlaceholderValue($value)
    {
        return $this->placeholderWriter->add($value);
    }

    /**
     * @param $operator
     *
     * @return string
     */
    public function writeConjunction($operator)
    {
        return ' '.$operator.' ';
    }

    /**
     * @return string
     */
    public function writeIsNull()
    {
        return ' IS NULL';
    }

    /**
     * @return string
     */
    public function writeIsNotNull()
    {
        return ' IS NOT NULL';
    }

    /**
     * Returns the column name.
     *
     * @param Column $column
     *
     * @return string
     */
    public function writeColumnName(Column $column)
    {
        $name = $column->getName();

        if ($name === Column::ALL) {
            return $this->writeColumnAll();
        }

        return $name;
    }

    /**
     * @return string
     */
    protected function writeColumnAll()
    {
        return '*';
    }

    /**
     * @param string $queryPart
     */
    protected function createQueryObject($queryPart)
    {
        if (null === $this->queryWriterInstances[$queryPart]) {
            $this->queryWriterInstances[$queryPart] = \call_user_func_array(
                \explode('::', $this->queryWriterArray[$queryPart]),
                [$this, $this->placeholderWriter]
            );
        }
    }
}
