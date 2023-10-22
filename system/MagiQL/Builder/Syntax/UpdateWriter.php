<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Manipulation\QueryException;
use Phacil\Framework\MagiQL\Manipulation\Update;
use Phacil\Framework\MagiQL\Syntax\SyntaxFactory;

/**
 * Class UpdateWriter.
 */
class UpdateWriter extends AbstractBaseWriter
{
    /**
     * @param Update $update
     *
     * @throws QueryException
     *
     * @return string
     */
    public function write(Update $update)
    {
        $values = $update->getValues();
        if (empty($values)) {
            throw new QueryException('No values to update in Update query.');
        }

        $parts = array(
            'UPDATE '.$this->writer->writeTable($update->getTable()).' SET ',
            $this->writeUpdateValues($update),
        );

        AbstractBaseWriter::writeWhereCondition($update, $this->writer, $this->placeholderWriter, $parts);
        AbstractBaseWriter::writeLimitCondition($update, $this->placeholderWriter, $parts);
        $comment = AbstractBaseWriter::writeQueryComment($update);

        return $comment.implode(' ', $parts);
    }

    /**
     * @param Update $update
     *
     * @return string
     */
    protected function writeUpdateValues(Update $update)
    {
        $assigns = [];
        foreach ($update->getValues() as $column => $value) {
            $newColumn = array($column);
            $column = $this->columnWriter->writeColumn(SyntaxFactory::createColumn($newColumn, $update->getTable()));

            $value = $this->writer->writePlaceholderValue($value);

            $assigns[] = "$column = $value";
        }

        return \implode(', ', $assigns);
    }
}
