<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder;

use Phacil\Framework\MagiQL\Syntax\Column;
use Phacil\Framework\MagiQL\Syntax\Table;

/**
 * Class MySqlBuilder.
 */
class MySqlBuilder extends \Phacil\Framework\MagiQL\Builder
{
    /**
     * {@inheritdoc}
     *
     * @param Column $column
     *
     * @return string
     */
    public function writeColumnName(Column $column)
    {
        if ($column->isAll()) {
            return '*';
        }

        if (false !== strpos($column->getName(), '(')) {
            return parent::writeColumnName($column);
        }

        return $this->wrapper(parent::writeColumnName($column));
    }

    /**
     * {@inheritdoc}
     *
     * @param Table $table
     *
     * @return string
     */
    public function writeTableName(Table $table)
    {
        return $this->wrapper(parent::writeTableName($table));
    }

    /**
     * {@inheritdoc}
     *
     * @param $alias
     *
     * @return string
     */
    public function writeTableAlias($alias)
    {
        return $this->wrapper(parent::writeTableAlias($alias));
    }

    /**
     * {@inheritdoc}
     *
     * @param $alias
     *
     * @return string
     */
    public function writeColumnAlias($alias)
    {
        return $this->wrapper($alias);
    }

    /**
     * @param        $string
     * @param string $char
     *
     * @return string
     */
    protected function wrapper($string, $char = '`')
    {
        if (0 === strlen($string)) {
            return '';
        }

        return $char.$string.$char;
    }
}
