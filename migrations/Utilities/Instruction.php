<?php

namespace Migrations\Utilities;

class Instruction
{
    public array $instructions = [];
    public array $constraints = [];
    public function __construct(public string $tableName)
    {
        
    }
    public function set(string $instruction)
    {
        array_push($this->instructions, $instruction);
    }

    public function setConstraint(string $instruction)
    {
        array_push($this->constraints, $instruction);
    }
}
