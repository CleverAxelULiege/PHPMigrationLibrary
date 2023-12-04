<?php

namespace Migrations\Utilities;

use Migrations\Utilities\TableCreateInterface;

class Table implements TableUpdateInterface{

    

    /**
     * @var \Migrations\Utilities\Column[]
     */
    public array $columns = [];
    const ADD = 0;
    const UPDATE = 1;
    const DELETE = 2;
    public function __construct(public string $name, public string $status)
    {
        
    }

    public function addColumn(string $name): ColumnCreateInterface
    {
        $column = new Column($name);
        array_push($this->columns, $column);
        // var_dump($this->columns);
        return $column;
    }

    public function updateColumn(string $name): ColumnUpdateInterface
    {
        $column = new Column($name);
        array_push($this->columns, $column);
        // var_dump($this->columns);
        return $column;
    }
}