<?php

namespace Seeds\Utilities;

class Seed implements SeedInterface{
    public string $table;

    public array $tableWithData = [];

    public function table(string $table){
        $this->table = $table;

        if(!isset($this->tableWithData[$table])){
            $this->tableWithData[$table] = [];
        }

        return $this;
    }

    /**
     * @param array{columnName: string, value: string} $data
     */
    public function with(array $data){
        array_push($this->tableWithData[$this->table], $data);
    }
}

    /**
     * @param array<string, string> $data*/