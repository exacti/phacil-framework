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
 * Class Intersect.
 */
class Intersect implements QueryInterface, QueryPartInterface
{
    const INTERSECT = 'INTERSECT';

    /**
     * @var array
     */
    protected $intersect = [];

    /**
     * @return string
     */
    public function partName()
    {
        return 'INTERSECT';
    }

    /**
     * @param Select $select
     *
     * @return $this
     */
    public function add(Select $select)
    {
        $this->intersect[] = $select;

        return $this;
    }

    /**
     * @return array
     */
    public function getIntersects()
    {
        return $this->intersect;
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Table
     */
    public function getTable()
    {
        throw new QueryException('INTERSECT does not support tables');
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function getWhere()
    {
        throw new QueryException('INTERSECT does not support WHERE.');
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function where()
    {
        throw new QueryException('INTERSECT does not support the WHERE statement.');
    }
}
