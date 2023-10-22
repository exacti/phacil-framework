<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Manipulation\Intersect;

/**
 * Class IntersectWriter.
 */
class IntersectWriter extends AbstractSetWriter
{
    /**
     * @param Intersect $intersect
     *
     * @return string
     */
    public function write(Intersect $intersect)
    {
        return $this->abstractWrite($intersect, 'getIntersects', Intersect::INTERSECT);
    }
}
