<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Manipulation;

/**
 * Class Union.
 */
class Union extends AbstractSetQuery
{
    const UNION = 'UNION';

    /**
     * @return string
     */
    public function partName()
    {
        return 'UNION';
    }
}