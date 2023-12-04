<?php

use Migrations\Utilities\Schema;

require(__DIR__ . "/../Utilities/ColumnCreateInterface.php");
require(__DIR__ . "/../Utilities/TableCreateInterface.php");
require(__DIR__ . "/../Utilities/ColumnType.php");

require(__DIR__ . "/../Utilities/Instruction.php");

require(__DIR__ . "/../Utilities/Migration.php");
require(__DIR__ . "/../Utilities/Schema.php");
require(__DIR__ . "/../Utilities/Table.php");
require(__DIR__ . "/../Utilities/Column.php");


$migrationFiles = array_filter(scandir(__DIR__. "/../"), fn($f) => is_file(__DIR__ . "/../" . $f));
$schemas = [];


foreach ($migrationFiles as $migrationFile) {
    require(__DIR__ . "/../" . $migrationFile);
    $className = "\\Migrations\\" . str_replace(".php", "", $migrationFile);

    /**
     * @var \Migrations\Utilities\Migration
     */
    $migration = new $className();
    $schema = new Schema();
    $migration->up($schema);
    var_dump($migration->createInstructions($schema)->getQueries());

    // var_dump($migration->instructions);
}