<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;

class migration_test extends Migration{
    
    public function up(Schema $schema)
    {
        $schema->createTable("my_table", function(TableCreateInterface $table){
            $table->addColumn("created_at")->timestamp()->default("CURRENT_TIMESTAMP(0)")->withTimeZone();
            $table->addColumn("decimal")->decimal(1, 1)->default("10");
        });
    }

    public function down(Schema $schema)
    {
        
    }
}