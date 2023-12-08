<?php

$seedName = $argv[1] ?? null;

if ($seedName == null) {
    throw new Exception("No seed name specified");
}

$date = date("YmdHis");

$seedFile = "Seed" . "_" . $date . "_" . $seedName;

try {
    $fstream = fopen(__DIR__ . "/../" . $seedFile . ".php", "w");

    $fileContent = file_get_contents(__DIR__ . "/../template/createTemplate.txt");
    fwrite($fstream, str_replace(":PLACE_HOLDER_CLASS", $seedFile, $fileContent));

} finally {
    fclose($fstream);
}
