<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Manipulation;

/**
 * Class AbstractCreationalQuery.
 */
abstract class AbstractCreationalQuery extends AbstractBaseQuery
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @param string $table
     * @param array  $values
     */
    public function __construct($table = null, array $values = null)
    {
        if (isset($table)) {
            $this->setTable($table);
        }

        if (!empty($values)) {
            $this->setValues($values);
        }
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setValues(array $values)
    {
        $this->values = $values;
        
        return $this;
    }
}
