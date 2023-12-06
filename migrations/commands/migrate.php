<?php

use Migrations\Utilities\Database\Config;
use Migrations\Utilities\Database\Database;
use Migrations\Utilities\MigrationOperation;

$migrationArg = $argv[1] ?? null;
$migrationStep = 0;

if($migrationArg != null && str_starts_with($migrationArg, "--do:")){
    $migrationStep = (int)substr($migrationArg,5);
    $migrationArg = substr($migrationArg, 0, 4);
}

$connectionToUse = $argv[2] ?? null;

if($connectionToUse != null){
    $connectionToUse = substr($connectionToUse, 2);
}

require(__DIR__ . "/../../vendor/autoload.php");
define("HISTORIC_PATH", __DIR__ . "/../history/historic.json");
define("CONNECTION", $connectionToUse ?? "default");

$db = new Database(
    Config::$conn[CONNECTION]["host"],
    Config::$conn[CONNECTION]["db_name"],
    Config::$conn[CONNECTION]["port"],
    Config::$conn[CONNECTION]["user"],
    Config::$conn[CONNECTION]["password"]
);

$migrationOperation = new MigrationOperation($db, HISTORIC_PATH);

$migrationOperation->db->beginTransaction();
try {
    switch($migrationArg){
        case null:
            $migrationOperation->migrate(null);
            break;

        case "--reset":
            $migrationOperation->rollbackAll();
            break;

        case "--do":
            $migrationOperation->doStep($migrationStep);
            break;

        case "--status":
            $migrationOperation->status();
            break;

        default:
            $migrationOperation->colorLog("Unknown args", "w");
        break;
    }
    $migrationOperation->db->commitTransaction();
} catch (Exception $e) {
    $migrationOperation->clearLog();
    $migrationOperation->colorLog("----------An error occured rolling back transaction (っ °Д °;)っ----------", "e");
    $migrationOperation->colorLog($e->getMessage(), "w");
    $migrationOperation->db->rollbackTransaction();
}
