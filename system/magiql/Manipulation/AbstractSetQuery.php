<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Manipulation;

use Phacil\Framework\MagiQL\Api\Syntax\QueryPartInterface;
use Phacil\Framework\MagiQL\Api\QueryInterface;

/**
 * Class AbstractSetQuery.
 */
abstract class AbstractSetQuery implements QueryInterface, QueryPartInterface
{
    /**
     * @var array
     */
    protected $union = [];

    /**
     * @param Select $select
     *
     * @return $this
     */
    public function add(Select $select)
    {
        $this->union[] = $select;

        return $this;
    }

    /**
     * @return array
     */
    public function getUnions()
    {
        return $this->union;
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Table
     */
    public function getTable()
    {
        throw new QueryException(
            \sprintf('%s does not support tables', $this->partName())
        );
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function getWhere()
    {
        throw new QueryException(
            \sprintf('%s does not support WHERE.', $this->partName())
        );
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function where()
    {
        throw new QueryException(
            \sprintf('%s does not support the WHERE statement.', $this->partName())
        );
    }
}
