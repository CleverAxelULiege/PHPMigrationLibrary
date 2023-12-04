<?php

namespace Migrations\Utilities;

use Migrations\Utilities\Column;

abstract class Migration
{
    /**
     * @var \Migrations\Utilities\Instruction[]
     */
    public array $instructions = [];

    public abstract function up(Schema $schema);
    public abstract function down(Schema $schema);

    public function createInstructions(Schema $schema)
    {
        $tables = $schema->getTables();

        foreach ($tables as $table) {
            $instruction = new Instruction($table->name);

            switch ($table->status) {
                case Table::ADD:
                    $this->createTables($table, $instruction);
                    break;
                
                default:
                    # code...
                    break;
            }
            
            array_push($this->instructions, $instruction);
        }
        return $this;
    }

    public function getQueries()
    {
        $queries = [];
        foreach ($this->instructions as $instruction) {
            $query = "CREATE TABLE " . $instruction->tableName . "(";
            $query .= implode(",", $instruction->instructions) . ($instruction->constraints == [] ? "" : ",");
            $query .= implode(",", $instruction->constraints);
            $query .= ");";
            array_push($queries, $query);
        }

        return $queries;
    }

    private function createTables(Table $table, Instruction $instruction){
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case ColumnType::$smallint:
                case ColumnType::$int:
                case ColumnType::$bigint:
                    $this->integerNumeric($column, $instruction);
                    break;

                case ColumnType::$char:
                case ColumnType::$varchar:
                    $this->textVariableLength($column, $instruction);
                    break;

                case ColumnType::$decimal:
                    $instruction->set(
                        $this->getNameAndType($column) .
                            "(" . $column->precision . "," . $column->scale . ")" . $this->getDefaultOrNullable($column)
                    );
                    break;

                default:
                    $this->defaultDataType($column, $instruction);
                    break;
            }

            $this->setConstraint($column, $instruction);
        }
    }

    private function defaultDataType(Column $column, Instruction $instruction)
    {
        $instruction->set($this->getNameAndType($column) . $this->getDefaultOrNullable($column));
    }

    private function integerNumeric(Column $column, Instruction $instruction)
    {
        if ($column->autoIncrement) {
            switch ($column->type) {
                case ColumnType::$smallint:
                    $instruction->set($column->name . " SMALLSERIAL");
                    break;

                case ColumnType::$int:
                    $instruction->set($column->name . " SERIAL");
                    break;

                case ColumnType::$bigint:
                    $instruction->set($column->name . " BIGSERIAL");
                    break;
            }
        } else {
            $instruction->set($this->getNameAndType($column) . $this->getDefaultOrNullable($column));
        }
    }

    private function textVariableLength(Column $column, Instruction $instruction)
    {
        $lengthParameter = "";
        if ($column->length != null) {
            $lengthParameter = "(" . $column->length . ")";
        }

        $instruction->set($this->getNameAndType($column) . $lengthParameter . $this->getDefaultOrNullable($column));
    }

    private function setConstraint(Column $column, Instruction $instruction)
    {
        if ($column->primaryKeyConstraint != null) {
            $instruction->setConstraint("CONSTRAINT " . $column->primaryKeyConstraint . " PRIMARY KEY(" . $column->name . ")");
        }

        if ($column->foreignKeyConstraint != null) {
            $instruction->setConstraint(
                "CONSTRAINT " . $column->foreignKeyConstraint .
                    " FOREIGN KEY(" . $column->name . ")" .
                    " REFERENCES " . $column->foreignKeyTableReference . "(" . $column->foreignKeyColumnReference . ")" .
                    ($column->cascadeOnDelete ? " ON DELETE CASCADE" : "") .
                    ($column->cascadeOnUpdate ? " ON UPDATE CASCADE" : "")
            );
        }
    }

    private function getDefaultOrNullable(Column $column)
    {
        return ($column->nullable ? " NULL" : " NOT NULL") .
            ($column->default != null ?
                ($column->withTimeZone ? " WITH TIME ZONE" : "")
                . " DEFAULT " . $column->default : ""
            );
    }

    private function getNameAndType(Column $column)
    {
        return $column->name . " " . $column->type;
    }
}
