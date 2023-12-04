<?php

namespace Migrations\Utilities;

use Migrations\Utilities\ColumnType;

class Column implements ColumnCreateInterface
{
    public string $type;
    public ?int $length = null;
    public ?int $precision = null;
    public ?int $scale = null;

    public bool $nullable = false;
    public bool $autoIncrement = false;
    public bool $cascadeOnDelete = false;
    public bool $cascadeOnUpdate = false;
    public bool $withTimeZone = false;

    public ?string $primaryKeyConstraint = null;
    public ?string $foreignKeyConstraint = null;
    public ?string $foreignKeyTableReference = null;
    public ?string $foreignKeyColumnReference = null;

    public ?string $default = null;


    public function __construct(public string $name)
    {
    }

    public function bool()
    {
        $this->type = ColumnType::$bool;
        return $this;
    }

    public function smallint()
    {
        $this->type = ColumnType::$smallint;
        return $this;
    }

    public function int()
    {
        $this->type = ColumnType::$int;
        return $this;
    }

    public function bigint()
    {
        $this->type = ColumnType::$bigint;
        return $this;
    }

    public function float()
    {
        $this->type = ColumnType::$float;
        return $this;
    }

    public function double()
    {
        $this->type = ColumnType::$double;
        return $this;
    }

    public function decimal(int $precision, int $scale)
    {
        $this->type = ColumnType::$decimal;
        $this->precision = $precision;
        $this->scale = $scale;
        return $this;
    }

    public function char(int $length)
    {
        $this->type = ColumnType::$char;
        $this->length = $length;
        return $this;
    }

    public function varchar(?int $length = null)
    {
        $this->type = ColumnType::$varchar;
        $this->length = $length;
        return $this;
    }

    public function text()
    {
        $this->type = ColumnType::$text;
        return $this;
    }

    public function timestamp()
    {
        $this->type = ColumnType::$timestamp;
        return $this;
    }

    public function date()
    {
        $this->type = ColumnType::$date;
        return $this;
    }

    public function time()
    {
        $this->type = ColumnType::$time;
        return $this;
    }

    public function nullable(bool $nullable = true)
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function primaryKey(?string $constraint = null)
    {
        if($constraint == null){
            $this->primaryKeyConstraint = "pk_" . $this->name;
        }
        return $this;
    }

    public function autoIncrement()
    {
        $this->autoIncrement = true;
        return $this;
    }

    public function foreignKey(string $reference, string $column, ?string $constraint = null)
    {
        $this->foreignKeyTableReference = $reference;
        $this->foreignKeyColumnReference = $column;

        if($constraint == null){
            $this->foreignKeyConstraint = "fk_" . $this->name;
        }

        return $this;
    }

    public function default(string $default)
    {
        $this->default = $default;
        return $this;
    }

    public function onCascadeDelete()
    {
        $this->cascadeOnDelete = true;
        return $this;
    }

    public function onCascadeUpdate()
    {
        $this->cascadeOnUpdate = true;
        return $this;
    }

    public function withTimeZone()
    {
        $this->withTimeZone = true;
        return $this;
    }
}
