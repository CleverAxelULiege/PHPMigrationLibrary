<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;

class Migration_20231205122623_create_table_books extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("books", function (TableCreateInterface $table) {
            $table->addColumn("title")->varchar(256)->nullable();
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropTable("books");
    }
}
