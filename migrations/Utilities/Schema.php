<?php

namespace Migrations\Utilities;

class Schema
{
    /**
     * @var \Migrations\Utilities\Table[]
     */
    private array $tables = [];
    
    public function createTable(string $name, callable $bluePrint){
        $table = new Table($name);
        array_push($this->tables, $table);
        $bluePrint($table);
    }

    public function getTables(){
        return $this->tables;
    }
}
