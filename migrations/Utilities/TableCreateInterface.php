<?php

namespace Migrations\Utilities;

use Migrations\Utilities\ColumnCreateInterface;

interface TableCreateInterface
{
    public function addColumn(string $name): ColumnCreateInterface;
}
