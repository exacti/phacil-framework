<?php

/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Builder\Syntax;

use Phacil\Framework\MagiQL\Api\BuilderInterface;

/**
 * Class WriterFactory.
 */
final class WriterFactory
{
    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return ColumnWriter
     */
    public static function createColumnWriter(\Phacil\Framework\MagiQL\Api\BuilderInterface $writer, PlaceholderWriter $placeholderWriter)
    {
        return new ColumnWriter($writer, $placeholderWriter);
    }

    /**
     * @param BuilderInterface    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return WhereWriter
     */
    public static function createWhereWriter(BuilderInterface $writer, PlaceholderWriter $placeholderWriter)
    {
        return new WhereWriter($writer, $placeholderWriter);
    }

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return SelectWriter
     */
    public static function createSelectWriter(BuilderInterface $writer, PlaceholderWriter $placeholderWriter)
    {
        return new SelectWriter($writer, $placeholderWriter);
    }

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return InsertWriter
     */
    public static function createInsertWriter(\Phacil\Framework\MagiQL\Api\BuilderInterface $writer, PlaceholderWriter $placeholderWriter)
    {
        return new InsertWriter($writer, $placeholderWriter);
    }

    /**
     * @param BuilderInterface $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return UpdateWriter
     */
    public static function createUpdateWriter(BuilderInterface $writer, PlaceholderWriter $placeholderWriter)
    {
        return new UpdateWriter($writer, $placeholderWriter);
    }

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return DeleteWriter
     */
    public static function createDeleteWriter(BuilderInterface $writer, PlaceholderWriter $placeholderWriter)
    {
        return new DeleteWriter($writer, $placeholderWriter);
    }

    /**
     * @return PlaceholderWriter
     */
    public static function createPlaceholderWriter()
    {
        return new PlaceholderWriter();
    }

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $writer
     *
     * @return IntersectWriter
     */
    public static function createIntersectWriter(\Phacil\Framework\MagiQL\Api\BuilderInterface $writer)
    {
        return new IntersectWriter($writer);
    }

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $writer
     *
     * @return MinusWriter
     */
    public static function createMinusWriter(\Phacil\Framework\MagiQL\Api\BuilderInterface $writer)
    {
        return new MinusWriter($writer);
    }

    /**
     * @param BuilderInterface $writer
     *
     * @return UnionWriter
     */
    public static function createUnionWriter(BuilderInterface $writer)
    {
        return new UnionWriter($writer);
    }

    /**
     * @param \Phacil\Framework\MagiQL\Api\BuilderInterface $writer
     *
     * @return UnionAllWriter
     */
    public static function createUnionAllWriter(BuilderInterface $writer)
    {
        return new UnionAllWriter($writer);
    }
}
