<?php
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use \Phacil\Framework\MagiQL\Api\BuilderInterface;
use Phacil\Framework\MagiQL\Api\Syntax\QueryPartInterface;

/**
 * Class AbstractSetWriter.
 */
abstract class AbstractSetWriter
{
    /**
     * @var BuilderInterface
     */
    protected $writer;

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $writer
     */
    public function __construct(BuilderInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param QueryPartInterface $setClass
     * @param string             $setOperation
     * @param $glue
     *
     * @return string
     */
    protected function abstractWrite(QueryPartInterface $setClass, $setOperation, $glue)
    {
        $selects = [];

        foreach ($setClass->$setOperation() as $select) {
            $selects[] = $this->writer->write($select, false);
        }

        return \implode("\n".$glue."\n", $selects);
    }
}
