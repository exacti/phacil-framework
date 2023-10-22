<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Manipulation;

use Phacil\Framework\MagiQL\Syntax\Where;
use Phacil\Framework\MagiQL\Api\QueryInterface;

/**
 * Class QueryFactory.
 */
final class QueryFactory
{
    /**
     * @param string $table
     * @param array  $columns
     *
     * @return Select
     */
    public static function createSelect($table = null, array $columns = null)
    {
        return new Select($table, $columns);
    }

    /**
     * @param string $table
     * @param array  $values
     *
     * @return Insert
     */
    public static function createInsert($table = null, array $values = null)
    {
        return new Insert($table, $values);
    }

    /**
     * @param string $table
     * @param array  $values
     *
     * @return Update
     */
    public static function createUpdate($table = null, array $values = null)
    {
        return new Update($table, $values);
    }

    /**
     * @param string $table
     *
     * @return Delete
     */
    public static function createDelete($table = null)
    {
        return new Delete($table);
    }

    /**
     * @param QueryInterface $query
     *
     * @return \Phacil\Framework\MagiQL\Api\WhereInterface
     */
    public static function createWhere(QueryInterface $query)
    {
        return new Where($query);
    }

    /**
     * @return Intersect
     */
    public static function createIntersect()
    {
        return new Intersect();
    }

    /**
     * @param Select $first
     * @param Select $second
     *
     * @return Minus
     */
    public static function createMinus(Select $first, Select $second)
    {
        return new Minus($first, $second);
    }

    /**
     * @return Union
     */
    public static function createUnion()
    {
        return new Union();
    }

    /**
     * @return UnionAll
     */
    public static function createUnionAll()
    {
        return new UnionAll();
    }
}
