<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\SeedInterface;

class Seed_20231208123156_test extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("users")->with([
            [
                "username" => "Billy",
                "password" => "12345",
                "email" => "email@email.com",
            ],
            [
                "username" => "bob",
                "password" => "54321",
                "email" => "wow@mail.com",
            ],
        ]);
    }
}