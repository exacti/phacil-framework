<?php 
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Builder;
use Phacil\Framework\MagiQL\Manipulation\AbstractBaseQuery;

/**
 * Class AbstractBaseWriter.
 */
abstract class AbstractBaseWriter
{
    /**
     * @var Builder
     */
    protected $writer;

    /**
     * @var PlaceholderWriter
     */
    protected $placeholderWriter;

    /**
     * @var ColumnWriter
     */
    protected $columnWriter;

    /**
     * @param Builder    $writer
     * @param PlaceholderWriter $placeholder
     */
    public function __construct(\Phacil\Framework\MagiQL\Api\BuilderInterface $writer, PlaceholderWriter $placeholder)
    {
        $this->writer = $writer;
        $this->placeholderWriter = $placeholder;

        $this->columnWriter = WriterFactory::createColumnWriter($writer, $placeholder);
    }

    /**
     * @param AbstractBaseQuery $class
     *
     * @return string
     */
    public static function writeQueryComment(AbstractBaseQuery $class)
    {
        $comment = '';
        if ('' !== $class->getComment()) {
            $comment = $class->getComment();
        }

        return $comment;
    }

    /**
     * @param AbstractBaseQuery $class
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface    $writer
     * @param PlaceholderWriter $placeholderWriter
     * @param array             $parts
     */
    public static function writeWhereCondition(
        AbstractBaseQuery $class,
        \Phacil\Framework\MagiQL\Api\BuilderInterface $writer, 
        PlaceholderWriter $placeholderWriter,
        array &$parts
    ) {
        if (!is_null($class->getWhere())) {
            $whereWriter = WriterFactory::createWhereWriter($writer, $placeholderWriter);
            $parts[] = "WHERE {$whereWriter->writeWhere($class->getWhere())}";
        }
    }

    /**
     * @param AbstractBaseQuery $class
     * @param PlaceholderWriter $placeholderWriter
     * @param array             $parts
     */
    public static function writeLimitCondition(
        AbstractBaseQuery $class,
        PlaceholderWriter $placeholderWriter,
        array &$parts
    ) {
        if (!is_null($class->getLimitStart())) {
            $start = $placeholderWriter->add($class->getLimitStart());
            $parts[] = "LIMIT {$start}";
        }
    }
}
