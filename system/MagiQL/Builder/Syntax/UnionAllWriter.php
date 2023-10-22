<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Manipulation\UnionAll;

/**
 * Class UnionAllWriter.
 */
class UnionAllWriter extends AbstractSetWriter
{
    /**
     * @param UnionAll $unionAll
     *
     * @return string
     */
    public function write(UnionAll $unionAll)
    {
        return $this->abstractWrite($unionAll, 'getUnions', UnionAll::UNION_ALL);
    }
}
