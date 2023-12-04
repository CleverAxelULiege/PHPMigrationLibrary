<?php

namespace Migrations\Utilities;

use Migrations\Utilities\TableCreateInterface;

class Table implements TableCreateInterface{
    /**
     * @var \Migrations\Utilities\Column[]
     */
    public array $columns = [];
    public function __construct(public string $name)
    {
        
    }

    public function addColumn(string $name): ColumnCreateInterface
    {
        $column = new Column($name);
        array_push($this->columns, $column);
        // var_dump($this->columns);
        return $column;
    }
}