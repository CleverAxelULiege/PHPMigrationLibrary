<?php
define("NORMAL_TEMPLATE", -1);
define("CREATE_TEMPLATE", 0);
define("UPDATE_TEMPLATE", 2);

$date = date("YmdHis");
$migrationName = $argv[1] ?? null;


if ($migrationName == null) {
    throw new Exception("No migration name given");
}

$matches = [];
$match = preg_match("/.*(table_)(.+)/", $migrationName, $matches);


$migrationFile = "Migration_" . $date . "_" . $migrationName;

try {
    $fstream = fopen(__DIR__ . "/../" . $migrationFile . ".php", "w");
    switch(getTemplate($migrationName)){
        case CREATE_TEMPLATE:
            $fileContent = file_get_contents(__DIR__ . "/../template/createTemplate.txt");
            break;
        case UPDATE_TEMPLATE:
            $fileContent = file_get_contents(__DIR__ . "/../template/updateTemplate.txt");
            break;
        default:
            $fileContent = file_get_contents(__DIR__ . "/../template/normalTemplate.txt");
        break;
    }
    $fileContent = str_replace(":PLACE_HOLDER_CLASS", $migrationFile, $fileContent);
    if($match != 0){
        $fileContent = str_replace(":PLACE_HOLDER_TABLE", $matches[2], $fileContent);
    } else {
        $fileContent = str_replace(":PLACE_HOLDER_TABLE", "my_table", $fileContent);
    }
    fwrite($fstream, $fileContent);
} finally {
    fclose($fstream);
}

function getTemplate(string $migrationName){
    if(substr($migrationName, 0, 6) == "create"){
        return CREATE_TEMPLATE;
    }

    if(substr($migrationName, 0, 6) == "update"){
        return UPDATE_TEMPLATE;
    }

    return NORMAL_TEMPLATE;
}
