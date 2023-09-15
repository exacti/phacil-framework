<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Syntax;

/**
 * Class OrderBy.
 */
class OrderBy
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * @var Column
     */
    protected $column;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var bool
     */
    protected $useAlias;

    /**
     * @param Column $column
     * @param string $direction
     */
    public function __construct(Column $column, $direction)
    {
        $this->setColumn($column);
        $this->setDirection($direction);
    }

    /**
     * @return Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setDirection($direction)
    {
        if (!in_array($direction, array(self::ASC, self::DESC))) {
            throw new \InvalidArgumentException(
                "Specified direction '$direction' is not allowed. Only ASC or DESC are allowed."
            );
        }
        $this->direction = $direction;

        return $this;
    }
}
