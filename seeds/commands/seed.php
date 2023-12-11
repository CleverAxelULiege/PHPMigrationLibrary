<?php

use App\Database\Config;
use App\Database\Database;
use Seeds\Utilities\SeedOperation;

define("CONNECTION", "default");

require(__DIR__ . "/../../vendor/autoload.php");

$db = new Database(
    Config::$conn[CONNECTION]["host"],
    Config::$conn[CONNECTION]["db_name"],
    Config::$conn[CONNECTION]["port"],
    Config::$conn[CONNECTION]["user"],
    Config::$conn[CONNECTION]["password"]
);
$seedOperation = new SeedOperation($db);
$seedOperation->setAllSeedsFile();
$seedOperation->createSeeds()->insertSeeds();

echo "SUCCESSFULLY SEEDED THE DATABASE\n";
?>

