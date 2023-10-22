<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

/**
 * Class PlaceholderWriter.
 */
class PlaceholderWriter
{
    /**
     * @var int
     */
    protected $counter = 1;

    /**
     * @var array
     */
    protected $placeholders = [];

    /**
     * @return array
     */
    public function get()
    {
        return $this->placeholders;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->counter = 1;
        $this->placeholders = [];

        return $this;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function add($value)
    {
        $placeholderKey = ':v'.$this->counter;
        $this->placeholders[$placeholderKey] = $this->setValidSqlValue($value);

        ++$this->counter;

        return $placeholderKey;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function setValidSqlValue($value)
    {
        $value = $this->writeNullSqlString($value);
        $value = $this->writeStringAsSqlString($value);
        $value = $this->writeBooleanSqlString($value);

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function writeNullSqlString($value)
    {
        if (\is_null($value) || (\is_string($value) && empty($value))) {
            $value = $this->writeNull();
        }

        return $value;
    }

    /**
     * @return string
     */
    protected function writeNull()
    {
        return 'NULL';
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function writeStringAsSqlString($value)
    {
        if (\is_string($value)) {
            $value = $this->writeString($value);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function writeString($value)
    {
        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function writeBooleanSqlString($value)
    {
        if (\is_bool($value)) {
            $value = $this->writeBoolean($value);
        }

        return $value;
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    protected function writeBoolean($value)
    {
        $value = \filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return ($value) ? '1' : '0';
    }
}
