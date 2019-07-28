<?php

namespace Engelsystem\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use stdClass;

trait ChangesReferences
{
    /**
     * @param string $fromTable
     * @param string $fromColumn
     * @param string $targetTable
     * @param string $targetColumn
     * @param string $type
     */
    protected function changeReferences($fromTable, $fromColumn, $targetTable, $targetColumn, $type = 'unsignedInteger')
    {
        $references = $this->getReferencingTables($fromTable, $fromColumn);

        foreach ($references as $reference) {
            /** @var stdClass $reference */
            /** @var SchemaBuilder $schema */
            $schema = $this->schema;

            $schema->table($reference->table, function (Blueprint $table) use ($reference) {
                $table->dropForeign($reference->constraint);
            });

            $schema->table(
                $reference->table,
                function (Blueprint $table) use ($reference, $targetTable, $targetColumn, $type) {
                    $table->{$type}($reference->column)->change();

                    $table->foreign($reference->column)
                        ->references($targetColumn)->on($targetTable)
                        ->onUpdate('cascade')
                        ->onDelete('cascade');
                }
            );
        }
    }

    /**
     * @param string $table
     * @param string $column
     * @return array
     */
    protected function getReferencingTables($table, $column): array
    {
        /** @var SchemaBuilder $schema */
        $schema = $this->schema;

        return $schema->getConnection()
            ->select(
                '
                    SELECT
                            `TABLE_NAME` as "table",
                            `COLUMN_NAME` as "column",
                            `CONSTRAINT_NAME` as "constraint"
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE REFERENCED_TABLE_SCHEMA = ?
                    AND REFERENCED_TABLE_NAME = ?
                    AND REFERENCED_COLUMN_NAME = ?
                ',
                [
                    $schema->getConnection()->getDatabaseName(),
                    $table,
                    $column,
                ]
            );
    }
}
