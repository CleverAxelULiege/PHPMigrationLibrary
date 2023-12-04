<?php

namespace Migrations\Utilities;

class Schema
{
    /**
     * @var \Migrations\Utilities\Table[]
     */
    private array $tables = [];

    public function createTable(string $name, callable $callback)
    {
        $table = new Table($name, Table::ADD);
        array_push($this->tables, $table);
        $callback($table);
    }

    public function modifyTable(string $name, callable $callback)
    {
        $table = new Table($name, Table::UPDATE);
        array_push($this->tables, $table);
        $callback($table);
    }

    public function getTables()
    {
        return $this->tables;
    }
}
