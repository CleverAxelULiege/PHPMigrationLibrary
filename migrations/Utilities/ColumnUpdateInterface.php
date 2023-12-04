<?php

namespace Migrations\Utilities;

interface ColumnUpdateInterface extends ColumnCreateInterface
{
    /**
     * @return $this
     */
    public function drop();

    /**
     * @return $this
     */
    public function update();
}
