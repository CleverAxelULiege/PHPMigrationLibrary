<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;
use Migrations\Utilities\TableUpdateInterface;

class Migration_20231205122302_create_table_sessions extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("sessions", function (TableCreateInterface $table) {
            $table->addColumn("uuid")->varchar(36)->nullable(false);
            $table->addColumn("user_id")->int()->nullable(false)->foreignKey("users", "id")->onDeleteCascade()->onUpdateCascade();
        });
    }

    public function down(Schema $schema)
    {  
        $schema->updateTable("sessions", function(TableUpdateInterface $table){
            $table->updateColumn("user_id")->dropForeignKey();
            $table->updateColumn("user_id")->drop();
        });
        $schema->dropTable("sessions");
    }
}
