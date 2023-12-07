<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableUpdateInterface;

class Migration_20231205142219_update_table_books extends Migration
{

    public function up(Schema $schema)
    {
        $schema->updateTable("books", function (TableUpdateInterface $table) {
            $table->addColumn("author")->varchar(256)->nullable(false);
            $table->addColumn("from")->varchar(256)->nullable(false);
        });

        $schema->updateTable("users", function (TableUpdateInterface $table) {
            $table->updateColumn("uuid")->addUnique();
            $table->updateColumn("uuid")->rename("uuid_user");
        });

        $schema->renameTable("users", "super_users");

        
    }

    public function down(Schema $schema)
    {
        $schema->renameTable("super_users", "users");
        $schema->updateTable("books", function (TableUpdateInterface $table) {
            $table->updateColumn("author")->drop();
            $table->updateColumn("from")->drop();
        });
    }
}
