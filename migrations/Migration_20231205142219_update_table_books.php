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
        });

        
    }

    public function down(Schema $schema)
    {

    }
}
