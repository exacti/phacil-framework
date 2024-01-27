<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Api;

use Phacil\Framework\MagiQL\Api\QueryInterface;

/**
 * Interface BuilderInterface.
 */
interface BuilderInterface
{
    /**
     * @param QueryInterface $query
     *
     * @return string
     */
    public function write(QueryInterface $query);
    
    /**
     * @param \Phacil\Framework\MagiQL\Syntax\Table $table 
     * @return string 
     */
    public function writeTable(\Phacil\Framework\MagiQL\Syntax\Table $table);

    /**
     * Returns the table name.
     *
     * @param \Phacil\Framework\MagiQL\Syntax\Table $table
     *
     * @return string
     */
    public function writeTableName(\Phacil\Framework\MagiQL\Syntax\Table $table);

    /**
     * @param string $value
     *
     * @return string
     */
    public function writePlaceholderValue($value);

    /**
     * @param string $alias
     *
     * @return string
     */
    public function writeColumnAlias($alias);

    /**
     * @param $alias
     *
     * @return mixed
     */
    public function writeTableAlias($alias);

    /**
     * Returns the column name.
     *
     * @param \Phacil\Framework\MagiQL\Syntax\Column $column
     *
     * @return string
     */
    public function writeColumnName(\Phacil\Framework\MagiQL\Syntax\Column $column);
}