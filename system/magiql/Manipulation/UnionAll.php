<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Manipulation;

/**
 * Class UnionAll.
 */
class UnionAll extends AbstractSetQuery
{
    const UNION_ALL = 'UNION ALL';

    /**
     * @return string
     */
    public function partName()
    {
        return 'UNION ALL';
    }
}
