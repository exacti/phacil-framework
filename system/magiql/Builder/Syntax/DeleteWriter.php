<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Api\BuilderInterface;
use Phacil\Framework\MagiQL\Manipulation\Delete;

/**
 * Class DeleteWriter.
 */
class DeleteWriter
{
    /**
     * @var BuilderInterface
     */
    protected $writer;

    /**
     * @var PlaceholderWriter
     */
    protected $placeholderWriter;

    /**
     * @param BuilderInterface    $writer
     * @param PlaceholderWriter $placeholder
     */
    public function __construct(BuilderInterface $writer, PlaceholderWriter $placeholder)
    {
        $this->writer = $writer;
        $this->placeholderWriter = $placeholder;
    }

    /**
     * @param Delete $delete
     *
     * @return string
     */
    public function write(Delete $delete)
    {
        $table = $this->writer->writeTable($delete->getTable());
        $parts = array("DELETE FROM {$table}");

        AbstractBaseWriter::writeWhereCondition($delete, $this->writer, $this->placeholderWriter, $parts);
        AbstractBaseWriter::writeLimitCondition($delete, $this->placeholderWriter, $parts);
        $comment = AbstractBaseWriter::writeQueryComment($delete);

        return $comment.implode(' ', $parts);
    }
}
