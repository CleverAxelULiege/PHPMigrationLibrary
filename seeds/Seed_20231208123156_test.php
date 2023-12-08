<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\SeedInterface;

class Seed_20231208123156_test extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("my_table")->with([
            [
                "column_1" => "value",
                "column_2" => "value",
                "column_3" => "value",
            ],
            [
                "column_1" => "other_value",
                "column_2" => "other_value",
                "column_3" => "other_value",
            ],
            //etc..
        ]);

        $seed->table("my_table")->with([
            [
                "column_1" => "value_",
                "column_2" => "value_",
                "column_3" => "value_",
            ],
            [
                "column_1" => "other_value_",
                "column_2" => "other_value_",
                "column_3" => "other_value_",
            ],
            //etc..
        ]);
    }
}