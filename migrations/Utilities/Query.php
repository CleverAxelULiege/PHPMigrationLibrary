<?php

namespace Migrations\Utilities;

class Query
{
    public array $instructions = [];
    public array $constraints = [];
    public function setInstruction(string $instruction)
    {
        array_push($this->instructions, $instruction);
    }

    public function setConstraint(string $instruction)
    {
        array_push($this->constraints, $instruction);
    }
}
