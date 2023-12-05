<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;
use Migrations\Utilities\TableUpdateInterface;

class Migration_20231204122526_create_table_users extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("users", function (TableCreateInterface $table) {
            $table->addColumn("id")->int()->autoIncrement()->primaryKey();
            $table->addColumn("username")->varchar(128)->nullable(false);
            $table->addColumn("password")->varchar(128)->nullable(false);
            $table->addColumn("email")->varchar(128)->nullable();
        });

        $schema->modifyTable("users", function (TableUpdateInterface $table) {
            $table->updateColumn("username")->varchar(256);
            $table->addColumn("decimal_test")->decimal(10, 10)->nullable(true)->default("null");
        });
        $schema->modifyTable("users", function (TableUpdateInterface $table) {
            $table->updateColumn("decimal_test")->varchar(256);
        });

        
    }

    public function down(Schema $schema)
    {
    }
}
