<?php
$NORMAL_TEMPLATE = -1;
$CREATE_TEMPLATE = 0;
$UPDATE_TEMPLATE = 1;
$date = date("YmdHis");
$migrationName = $argv[1] ?? null;

if ($migrationName == null) {
    throw new Exception("No migration name given");
}

$template = $NORMAL_TEMPLATE;
$template = substr($migrationName, 0, 6) == "create" ? $CREATE_TEMPLATE : $NORMAL_TEMPLATE;
$template = substr($migrationName, 0, 6) == "update" ? $UPDATE_TEMPLATE : $NORMAL_TEMPLATE;


$migrationFile = "Migration_" . $date . "_" . $migrationName;

try {
    $fstream = fopen(__DIR__ . "/../" . $migrationFile . ".php", "w");
    switch($template){
        case $CREATE_TEMPLATE:
            $fileContent = file_get_contents(__DIR__ . "/../template/createTemplate.txt");
            break;
        case $UPDATE_TEMPLATE:
            $fileContent = file_get_contents(__DIR__ . "/../template/updateTemplate.txt");
            break;
        default:
            $fileContent = file_get_contents(__DIR__ . "/../template/normalTemplate.txt");
        break;
    }

    fwrite($fstream, str_replace(":PLACE_HOLDER", $migrationFile, $fileContent));
} finally {
    fclose($fstream);
}
