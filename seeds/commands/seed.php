<?php

use Seeds\Utilities\SeedOperation;

require(__DIR__ . "/../../vendor/autoload.php");

$seedOperation = new SeedOperation();
$seedOperation->setAllSeedsFile();
$seedOperation->createSeeds()->insertSeeds();