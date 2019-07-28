<?php

namespace Engelsystem\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

trait Reference
{
    /**
     * @param Blueprint $table
     * @param bool      $setPrimary
     * @return ColumnDefinition
     */
    protected function referencesUser(Blueprint $table, $setPrimary = false)
    {
        return $this->references($table, 'users', 'user_id', $setPrimary);
    }

    /**
     * @param Blueprint $table
     * @param string    $targetTable
     * @param string    $fromColumn
     * @param bool      $setPrimary
     * @return ColumnDefinition
     */
    protected function references(Blueprint $table, $targetTable, $fromColumn, $setPrimary = false)
    {
        $definition = $table->unsignedInteger($fromColumn);

        if ($setPrimary) {
            $table->primary($fromColumn);
        }

        $table->foreign($fromColumn)
            ->references('id')->on($targetTable)
            ->onUpdate('cascade')
            ->onDelete('cascade');

        return $definition;
    }
}
