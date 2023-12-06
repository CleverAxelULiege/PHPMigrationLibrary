<?php

namespace Migrations;

use Migrations\Utilities\DefaultDatetime;
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
            // $table->addColumn("password")->varchar(128)->nullable(false);
            // $table->addColumn("email")->varchar(128)->nullable(false);
            $table->addColumn("date")->date()->nullable(false)->default(DefaultDatetime::CURRENT_TIMESTAMP. "(0)");
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropTable("users");
    }
}
