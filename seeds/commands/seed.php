<?php

// use Normalizer;
use App\Database\Config;
use App\Database\Database;
use Seeds\Utilities\Random;
use Seeds\Utilities\SeedOperation;

define("CONNECTION", $connectionToUse ?? "default");

require(__DIR__ . "/../../vendor/autoload.php");

// $string = 'áéíóú';
// echo preg_replace('/[\x{0300}-\x{036f}]/u', "", normalizer_normalize($string , Normalizer::FORM_D));
// //aeiou


// die;

// var_dump(Random::password(10));
// echo Random::Name() . " " . Random::Name();

echo Random::birthdate() . " / " . Random::name() . " " . Random::name();
die;

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