<?php

namespace Engelsystem\Migrations;

use Engelsystem\Database\Migration\Migration;
use stdClass;

class FixOldCharacterSet extends Migration
{
    /**
     * Run the migration
     */
    public function up()
    {
        $connection = $this->schema->getConnection();
        $targetCharset = $connection->getConfig('charset');
        $targetCollation = $connection->getConfig('collation');

        if (!$targetCharset || !$targetCharset) {
            return;
        }

        $connection
            ->unprepared(sprintf(
                'ALTER DATABASE %s CHARACTER SET %s COLLATE %s',
                $connection->getDatabaseName(),
                $targetCharset,
                $targetCollation
            ));

        /** @var stdClass[] $databases */
        $tables = $this->getTablesToChange($targetCollation);
        foreach ($tables as $table) {
            $connection
                ->unprepared(sprintf(
                    'ALTER TABLE %s CONVERT TO CHARACTER SET %s COLLATE %s',
                    $table->table,
                    $targetCharset,
                    $targetCollation
                ));
        }
    }

    /**
     * Reverse the migration
     */
    public function down()
    {
        // Nothing to do
    }

    /**
     * @param $target
     * @return array
     */
    protected function getTablesToChange($target)
    {
        $connection = $this->schema->getConnection();

        return $connection
            ->select(
                '
                    SELECT
                        `TABLE_NAME` AS "table"
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = ?
                    AND TABLE_COLLATION != ?
                ',
                [
                    $connection->getDatabaseName(),
                    $target
                ]
            );
    }
}
