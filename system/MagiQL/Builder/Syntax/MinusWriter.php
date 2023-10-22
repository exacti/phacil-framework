<?php 
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use \Phacil\Framework\MagiQL\Api\BuilderInterface;
use Phacil\Framework\MagiQL\Manipulation\Minus;

/**
 * Class MinusWriter.
 */
class MinusWriter
{
    /**
     * @var BuilderInterface
     */
    protected $writer;

    /**
     * @param BuilderInterface $writer
     */
    public function __construct(BuilderInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param Minus $minus
     *
     * @return string
     */
    public function write(Minus $minus)
    {
        $first = $this->writer->write($minus->getFirst());
        $second = $this->writer->write($minus->getSecond());

        return $first."\n".Minus::MINUS."\n".$second;
    }
}
