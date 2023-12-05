<?php

namespace Migrations\Utilities;

use Migrations\Utilities\Column\ColumnCreate;
use Migrations\Utilities\Column\ColumnUpdate;
use Migrations\Utilities\Column\ColumnCreateInterface;
use Migrations\Utilities\Column\ColumnUpdateInterface;

class Table implements TableUpdateInterface{

    

    /**
     * @var \Migrations\Utilities\Column\ColumnBase[]
     */
    public array $columns = [];
    const ADD = 0;
    const UPDATE = 1;
    const DELETE = 2;
    public function __construct(public string $name, public int $operation)
    {
        
    }

    public function addColumn(string $name): ColumnCreateInterface
    {
        $column = new ColumnCreate($name);
        array_push($this->columns, $column);
        return $column;
    }

    public function updateColumn(string $name): ColumnUpdateInterface
    {
        $column = new ColumnUpdate($name);
        array_push($this->columns, $column);
        return $column;
    }
}