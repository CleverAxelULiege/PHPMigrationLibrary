<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;

class Migration_20231205141249_create_table_authors extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("authors", function (TableCreateInterface $table) {

        });
    }

    public function down(Schema $schema)
    {
        $schema->dropTable("authors");
    }
}
