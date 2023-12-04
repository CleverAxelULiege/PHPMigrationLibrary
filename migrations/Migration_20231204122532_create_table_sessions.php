<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;
use Migrations\Utilities\TableUpdateInterface;

class Migration_20231204122532_create_table_sessions extends Migration
{

    public function up(Schema $schema)
    {
        $schema->modifyTable("sessions", function (TableUpdateInterface $table) {
            $table->updateColumn("test")->update();
        });
    }

    public function down(Schema $schema)
    {
        
    }
}
