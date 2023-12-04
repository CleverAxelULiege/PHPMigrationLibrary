<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;

class Migration_20231204122542_create_table_books extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("books", function (TableCreateInterface $table) {

        });
    }

    public function down(Schema $schema)
    {
        
    }
}
