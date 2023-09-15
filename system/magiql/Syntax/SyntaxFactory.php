<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Syntax;

/**
 * Class SyntaxFactory.
 */
final class SyntaxFactory
{
    /**
     * Creates a collection of Column objects.
     *
     * @param array      $arguments
     * @param Table|null $table
     *
     * @return array
     */
    public static function createColumns(array &$arguments, $table = null)
    {
        $createdColumns = [];

        foreach ($arguments as $index => $column) {
            if (!is_object($column)) {
                $newColumn = array($column);
                $column = self::createColumn($newColumn, $table);
                if (!is_numeric($index)) {
                    $column->setAlias($index);
                }

                $createdColumns[] = $column;
            } else if ($column instanceof Column) {
                $createdColumns[] = $column;
            }
        }

        return \array_filter($createdColumns);
    }

    /**
     * Creates a Column object.
     *
     * @param array      $argument
     * @param null|Table $table
     *
     * @return Column
     */
    public static function createColumn(array &$argument, $table = null)
    {
        $columnName = \array_values($argument);
        $columnName = $columnName[0];

        $columnAlias = \array_keys($argument);
        $columnAlias = $columnAlias[0];

        if (\is_numeric($columnAlias) || \strpos($columnName, '*') !== false) {
            $columnAlias = null;
        }

        return new Column($columnName, (string) $table, $columnAlias);
    }

    /**
     * Creates a Table object.
     *
     * @param string[] $table
     *
     * @return Table
     */
    public static function createTable($table)
    {
        $tableName = $table;
        if (\is_array($table)) {
            $tableName = \current($table);
            $tableAlias = \key($table);
        }

        $newTable = new Table($tableName);

        if (isset($tableAlias) && !is_numeric($tableAlias)) {
            $newTable->setAlias($tableAlias);
        }

        return $newTable;
    }
}
