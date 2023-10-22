<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use \Phacil\Framework\MagiQL\Api\BuilderInterface;
use Phacil\Framework\MagiQL\Manipulation\Insert;
use Phacil\Framework\MagiQL\Manipulation\QueryException;

/**
 * Class InsertWriter.
 */
class InsertWriter
{
    /**
     * @var BuilderInterface
     */
    protected $writer;

    /**
     * @var ColumnWriter
     */
    protected $columnWriter;

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface    $writer
     * @param PlaceholderWriter $placeholder
     */
    public function __construct(BuilderInterface $writer, PlaceholderWriter $placeholder)
    {
        $this->writer = $writer;
        $this->columnWriter = WriterFactory::createColumnWriter($this->writer, $placeholder);
    }

    /**
     * @param Insert $insert
     *
     * @throws QueryException
     *
     * @return string
     */
    public function write(Insert $insert)
    {
        $columns = $insert->getColumns();

        if (empty($columns)) {
            throw new QueryException('No columns were defined for the current schema.');
        }

        $columns = $this->writeQueryColumns($columns);
        $values = $this->writeQueryValues($insert->getValues());
        $table = $this->writer->writeTable($insert->getTable());
        $comment = AbstractBaseWriter::writeQueryComment($insert);

        return $comment."INSERT INTO {$table} ($columns) VALUES ($values)";
    }

    /**
     * @param $columns
     *
     * @return string
     */
    protected function writeQueryColumns($columns)
    {
        return $this->writeCommaSeparatedValues($columns, $this->columnWriter, 'writeColumn');
    }

    /**
     * @param $collection
     * @param $writer
     * @param string $method
     *
     * @return string
     */
    protected function writeCommaSeparatedValues($collection, $writer, $method)
    {
        \array_walk(
            $collection,
            function (&$data) use ($writer, $method) {
                $data = $writer->$method($data);
            }
        );

        return \implode(', ', $collection);
    }

    /**
     * @param $values
     *
     * @return string
     */
    protected function writeQueryValues($values)
    {
        return $this->writeCommaSeparatedValues($values, $this->writer, 'writePlaceholderValue');
    }
}
