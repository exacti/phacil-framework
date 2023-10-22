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
 * Class Minus.
 */
class Minus implements QueryInterface, QueryPartInterface
{
    const MINUS = 'MINUS';

    /**
     * @var Select
     */
    protected $first;

    /**
     * @var Select
     */
    protected $second;

    /**
     * @return string
     */
    public function partName()
    {
        return 'MINUS';
    }

    /***
     * @param Select $first
     * @param Select $second
     */
    public function __construct(Select $first, Select $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * @return \Phacil\Framework\MagiQL\Manipulation\Select
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @return \Phacil\Framework\MagiQL\Manipulation\Select
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Table
     */
    public function getTable()
    {
        throw new QueryException('MINUS does not support tables');
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function getWhere()
    {
        throw new QueryException('MINUS does not support WHERE.');
    }

    /**
     * @throws QueryException
     *
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function where()
    {
        throw new QueryException('MINUS does not support the WHERE statement.');
    }
}
