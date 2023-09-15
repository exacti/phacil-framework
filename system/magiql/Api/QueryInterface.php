<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Api;

/**
 * Interface QueryInterface.
 */
interface QueryInterface
{
    /**
     * @return string
     */
    public function partName();

    /**
     * @return \Phacil\Framework\MagiQL\Syntax\Table
     */
    public function getTable();

    /**
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function getWhere();

    /**
     * @return \Phacil\Framework\MagiQL\Syntax\Where
     */
    public function where();
}
