<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\DataGenerator;
use Seeds\Utilities\Random;
use Seeds\Utilities\SeedInterface;

class Seed_20231208123156_test extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("users")->with(DataGenerator::generate(function () {
            return [
                "username" => Random::name() . " " . Random::firstname(),
                "password" => Random::password(),
                "email" => Random::email(),
            ];
        }, 30));
    }
}
