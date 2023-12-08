<?php

use App\Database\Config;
use App\Database\Database;
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
define("CONNECTION", $connectionToUse ?? "default");

$db = new Database(
    Config::$conn[CONNECTION]["host"],
    Config::$conn[CONNECTION]["db_name"],
    Config::$conn[CONNECTION]["port"],
    Config::$conn[CONNECTION]["user"],
    Config::$conn[CONNECTION]["password"]
);

$migrationOperation = new MigrationOperation($db);

$migrationOperation->db->beginTransaction();
try {
    switch($migrationArg){
        case "--all":
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
            $migrationOperation->colorLog("Unknown args. Possible args are :", "w");
            $migrationOperation->colorLog("--all -> MIGRATE ALL REMAINING MIGRATIONS", "w");
            $migrationOperation->colorLog("--reset -> RESET ALL MIGRATIONS", "w");
            $migrationOperation->colorLog("--do:<integer> -> ADVANCE OR ROLLBACK FROM THE INTEGER PASSED", "w");
            $migrationOperation->colorLog("--status -> CHECK THE STATUS OF THE MIGRATIONS", "w");
        break;
    }
    $migrationOperation->db->commitTransaction();
} catch (Exception $e) {
    $migrationOperation->clearLog();
    $migrationOperation->colorLog("----------An error occured rolling back transaction (っ °Д °;)っ----------\n", "e");
    $migrationOperation->colorLog("The error occured in this file : " . $migrationOperation->currentMigrationFile, "i");
    $migrationOperation->colorLog($e->getMessage(), "w");
    $migrationOperation->db->rollbackTransaction();
}
