<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Manipulation;

use Phacil\Framework\MagiQL\Syntax\Where;
use Phacil\Framework\MagiQL\Syntax\Column;
use Phacil\Framework\MagiQL\Syntax\SyntaxFactory;

/**
 * Class JoinQuery.
 */
class JoinQuery
{
    const JOIN_LEFT = 'LEFT';
    const JOIN_RIGHT = 'RIGHT';
    const JOIN_INNER = 'INNER';
    const JOIN_CROSS = 'CROSS';

    /**
     * @var Where
     */
    protected $joinCondition;

    /**
     * @var bool
     */
    protected $isJoin = false;

    /**
     * @var string
     */
    protected $joinType;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var Select
     */
    protected $select;

    /**
     * @param Select $select
     */
    public function __construct(Select $select)
    {
        $this->select = $select;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->select->setTable($table);

        return $this;
    }

    /**
     * @param string   $table
     * @param mixed    $selfColumn
     * @param mixed    $refColumn
     * @param string[] $columns
     *
     * @return Select
     */
    public function leftJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_LEFT);
    }

    /**
     * @param string   $table
     * @param mixed    $selfColumn
     * @param mixed    $refColumn
     * @param string[] $columns
     * @param string   $joinType
     *
     * @return Select
     */
    public function join(
        $table,
        $selfColumn = null,
        $refColumn = null,
        $columns = [],
        $joinType = null
    ) {
        if (!isset($this->joins[$table])) {
            $select = QueryFactory::createSelect($table);
            $select->setColumns($columns);
            $select->setJoinType($joinType);
            $select->setParentQuery($this->select);
            $this->addJoin($select, $selfColumn, $refColumn);
        }

        return $this->joins[$table];
    }

    /**
     * @param Select $select
     * @param mixed  $selfColumn
     * @param mixed  $refColumn
     *
     * @return Select
     */
    public function addJoin(Select $select, $selfColumn, $refColumn)
    {
        $select->isJoin(true);
        $table = $select->getTable()->getName();

        if (!isset($this->joins[$table])) {
            if (!$selfColumn instanceof Column) {
                $newColumn = array($selfColumn);
                $selfColumn = SyntaxFactory::createColumn(
                    $newColumn,
                    $this->select->getTable()
                );
            }

            $select->joinCondition()->equals($refColumn, $selfColumn);
            $this->joins[$table] = $select;
        }

        return $this->joins[$table];
    }

    /**
     * Transforms Select in a joint.
     *
     * @param bool $isJoin
     *
     * @return $this
     */
    public function setJoin($isJoin = true)
    {
        $this->isJoin = $isJoin;

        return $this;
    }

    /**
     * @param string   $table
     * @param mixed    $selfColumn
     * @param mixed    $refColumn
     * @param string[] $columns
     *
     * @internal param null $selectClass
     *
     * @return Select
     */
    public function rightJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_RIGHT);
    }

    /**
     * @param string   $table
     * @param mixed    $selfColumn
     * @param mixed    $refColumn
     * @param string[] $columns
     *
     * @return Select
     */
    public function crossJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_CROSS);
    }

    /**
     * @param string   $table
     * @param mixed    $selfColumn
     * @param mixed    $refColumn
     * @param string[] $columns
     *
     * @return Select
     */
    public function innerJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_INNER);
    }

    /**
     * Alias to joinCondition.
     *
     * @return Where
     */
    public function on()
    {
        return $this->joinCondition();
    }

    /**
     * WHERE constrains used for the ON clause of a (LEFT/RIGHT/INNER/CROSS) JOIN.
     *
     * @return Where
     */
    public function joinCondition()
    {
        if (!isset($this->joinCondition)) {
            $this->joinCondition = QueryFactory::createWhere($this->select);
        }

        return $this->joinCondition;
    }

    /**
     * @return bool
     */
    public function isJoinSelect()
    {
        return $this->isJoin;
    }

    /**
     * @return bool
     */
    public function isJoin()
    {
        return $this->isJoin;
    }

    /**
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function getJoinCondition()
    {
        return $this->joinCondition;
    }

    /**
     * @param \Phacil\Framework\MagiQL\Syntax\Where $joinCondition
     *
     * @return $this
     */
    public function setJoinCondition($joinCondition)
    {
        $this->joinCondition = $joinCondition;

        return $this;
    }

    /**
     * @return string
     */
    public function getJoinType()
    {
        return $this->joinType;
    }

    /**
     * @param string $joinType
     *
     * @return $this
     */
    public function setJoinType($joinType)
    {
        $this->joinType = $joinType;

        return $this;
    }

    /**
     * @return array
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @param array $joins
     *
     * @return $this
     */
    public function setJoins($joins)
    {
        $this->joins = $joins;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllJoins()
    {
        $joins = $this->joins;

        foreach ($this->joins as $join) {
            $joins = \array_merge($joins, $join->getAllJoins());
        }

        return $joins;
    }
}
