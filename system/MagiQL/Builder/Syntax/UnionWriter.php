<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Manipulation\Union;

/**
 * Class UnionWriter.
 */
class UnionWriter extends AbstractSetWriter
{
    /**
     * @param Union $union
     *
     * @return string
     */
    public function write(Union $union)
    {
        return $this->abstractWrite($union, 'getUnions', Union::UNION);
    }
}
