<?php

namespace Migrations\Utilities;

use Migrations\Utilities\ColumnCreateInterface;

interface TableUpdateInterface extends TableCreateInterface
{
    public function updateColumn(string $name): ColumnUpdateInterface;
}
