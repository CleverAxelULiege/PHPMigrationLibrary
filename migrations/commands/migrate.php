<?php

use Migrations\migration_test;
use Migrations\Utilities\Schema;

require(__DIR__ . "/../Utilities/ColumnCreateInterface.php");
require(__DIR__ . "/../Utilities/TableCreateInterface.php");
require(__DIR__ . "/../Utilities/ColumnType.php");

require(__DIR__ . "/../Utilities/Query.php");

require(__DIR__ . "/../Utilities/Blueprint.php");
require(__DIR__ . "/../Utilities/Migration.php");
require(__DIR__ . "/../Utilities/Schema.php");
require(__DIR__ . "/../Utilities/Table.php");
require(__DIR__ . "/../Utilities/Column.php");


require(__DIR__ . "/../migration_test.php");

$migrationTest = new migration_test();
$schema = new Schema();
$migrationTest->up($schema);

$migrationTest->createQuery($schema);
var_dump($migrationTest->queries);