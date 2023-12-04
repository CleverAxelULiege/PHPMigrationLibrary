<?php

$date = date("YmdHis");
$migrationName = $argv[1] ?? null;

if ($migrationName == null) {
    throw new Exception("No migration name given");
}

$isCreateTemplate = substr($migrationName, 0, 6) == "create";


$migrationFile = "Migration_" . $date . "_" . $migrationName;

try {
    $fstream = fopen(__DIR__ . "/../" . $migrationFile . ".php", "w");
    
    if ($isCreateTemplate) {
        $fileContent = file_get_contents(__DIR__ . "/../template/createTemplate.txt");
    } else {
        $fileContent = file_get_contents(__DIR__ . "/../template/normalTemplate.txt");
    }

    fwrite($fstream, str_replace(":PLACE_HOLDER", $migrationFile, $fileContent));
} finally {
    fclose($fstream);
}
