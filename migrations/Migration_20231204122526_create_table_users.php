<?php

namespace Migrations;

use Migrations\Utilities\Defaults\DefaultDatetime;
use Migrations\Utilities\Defaults\DefaultUUID;
use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;

class Migration_20231204122526_create_table_users extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("users", function (TableCreateInterface $table) {
            $table->addColumn("id")->int()->autoIncrement()->primaryKey();
            $table->addColumn("username")->varchar(128)->nullable(false);
            // $table->addColumn("password")->varchar(128)->nullable(false);
            // $table->addColumn("email")->varchar(128)->nullable(false);
            $table->addColumn("uuid_v1")->uuid()->nullable(false)->default(DefaultUUID::UUID_V1);
            $table->addColumn("uuid_v4")->uuid()->nullable(false)->default(DefaultUUID::UUID_V4);
            $table->addColumn("timestamp")->timestamp()->default(DefaultDatetime::getCurrentTimestamp(0));
            $table->addColumn("date")->date()->default(DefaultDatetime::getCurrentDate());
            $table->addColumn("time")->time()->default(DefaultDatetime::getCurrentTime(0));
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropTable("users");
    }
}
