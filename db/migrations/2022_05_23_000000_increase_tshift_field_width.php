<?php

namespace Engelsystem\Migrations;

use Engelsystem\Database\Migration\Migration;

/**
 * To allow for larger key names such as "2XL-G"
 */
class IncreaseTshiftFieldWidth extends Migration
{
    /**
     * Run the migration
     */
    public function up()
    {
        if (!$this->schema->hasTable('GroupPrivileges')) {
            return;
        }

        $connection = $this->schema->getConnection();

        $connection->statement(
            'ALTER TABLE users_personal_data MODIFY shirt_size VARCHAR(6) ;'
        );
    }

    /**
     * Reverse the migration
     */
    public function down()
    {
        if (!$this->schema->hasTable('GroupPrivileges')) {
            return;
        }

        $connection = $this->schema->getConnection();
        $connection->statement(
            'ALTER TABLE users_personal_data MODIFY shirt_size VARCHAR(4) ;'
        );
    }
}
