<?php

namespace App\Database;

class Config
{
    public static array $conn = [
        "default" => [
            "host" => "localhost",
            "db_name" => "migration",
            "port" => "5432",
            "user" => "postgres",
            "password" => "admin",
        ]
    ];
}
