<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Manipulation;

use Phacil\Framework\MagiQL\Syntax\OrderBy;
use Phacil\Framework\MagiQL\Api\Syntax\QueryPartInterface;
use Phacil\Framework\MagiQL\Syntax\SyntaxFactory;
use Phacil\Framework\MagiQL\Syntax\Table;
use Phacil\Framework\MagiQL\Syntax\Where;
use Phacil\Framework\MagiQL\Api\BuilderInterface;
use Phacil\Framework\MagiQL\Api\QueryInterface;

/**
 * Class AbstractBaseQuery.
 */
abstract class AbstractBaseQuery implements QueryInterface, QueryPartInterface
{
    /**
     * @var string
     */
    protected $comment = '';

    /**
     * @var \Phacil\Framework\MagiQL\Api\BuilderInterface
     */
    protected $builder;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $whereOperator = 'AND';

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var int
     */
    protected $limitStart;

    /**
     * @var int
     */
    protected $limitCount;

    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * @return Where
     */
    protected function filter()
    {
        if (!isset($this->where)) {
            $this->where = QueryFactory::createWhere($this);
        }

        return $this->where;
    }

    /**
     * Stores the builder that created this query.
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $builder 
     * @return $this 
     */
    final public function setBuilder(BuilderInterface $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * 
     * @return \Phacil\Framework\MagiQL\Api\BuilderInterface|\Phacil\Framework\MagiQL
     * @throws \Phacil\Framework\Exception\RuntimeException 
     */
    final public function getBuilder()
    {
        if (!$this->builder) {
            throw new \Phacil\Framework\Exception\RuntimeException('Query builder has not been injected with setBuilder');
        }

        return $this->builder;
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getSql();
        } catch (\Exception $e) {
            return \sprintf('[%s] %s', \get_class($e), $e->getMessage());
        }
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     * Optionally can return the SQL with formatted structure.
     *
     * @param bool $formatted
     *
     * @return string
     */
    public function getSql($formatted = false)
    {
        if ($formatted) {
            return $this->getBuilder()->writeFormatted($this);
        }

        return $this->getBuilder()->write($this);
    }

    /**
     * @return string
     */
    abstract public function partName();

    /**
     * @return Where
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param Where $where
     *
     * @return $this
     */
    public function setWhere(Where $where)
    {
        $this->where = $where;

        return $this;
    }

    /**
     * 
     * @return \Phacil\Framework\MagiQL\Syntax\Table|null
     */
    public function getTable()
    {
        $newTable = array($this->table);

        return \is_null($this->table) ? null : SyntaxFactory::createTable($newTable);
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = (string) $table;

        return $this;
    }

    /**
     * 
     * @param string $table 
     * @return $this 
     */
    public function from($table){
        return $this->setTable($table);
    }

    /**
     * @param string $whereOperator
     *
     * @return Where
     */
    public function where($whereOperator = 'AND')
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        $this->where->conjunction($whereOperator);

        return $this->where;
    }

    /**
     * @return string
     */
    public function getWhereOperator()
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        return $this->where->getConjunction();
    }

     /**
      * 
      * @param string $column 
      * @param string $direction 
      * @param null|Table $table 
      * @return $this 
      */
    public function orderBy($column, $direction = OrderBy::ASC, $table = null)
    {
        $newColumn = array($column);
        $column = SyntaxFactory::createColumn($newColumn, \is_null($table) ? $this->getTable() : $table);
        $this->orderBy[] = new OrderBy($column, $direction);

        return $this;
    }

    /**
     * @return int
     */
    public function getLimitCount()
    {
        return $this->limitCount;
    }

    /**
     * @return int
     */
    public function getLimitStart()
    {
        return $this->limitStart;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        // Make each line of the comment prefixed with "--",
        // and remove any trailing whitespace.
        $comment = '-- '.str_replace("\n", "\n-- ", \rtrim($comment));

        // Trim off any trailing "-- ", to ensure that the comment is valid.
        $this->comment = \rtrim($comment, '- ');

        if ($this->comment) {
            $this->comment .= "\n";
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * 
     * @return \Phacil\Framework\Databases\Object\ResultInterface|true|array 
     * @throws \Phacil\Framework\Exception 
     */
    public function load() {
        try {
            return $this->getBuilder()->execute($this);
        } catch (\Throwable $th) {
            throw new \Phacil\Framework\Exception($th->getMessage());
        }
        return [];
    }
}
