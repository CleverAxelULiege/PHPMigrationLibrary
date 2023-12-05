<?php

namespace Migrations\Utilities;

use Exception;
use Migrations\Utilities\Column\ColumnBase;
use Migrations\Utilities\Column\ColumnCreateInterface;
use Migrations\Utilities\Column\ColumnUpdateInterface;

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

            switch ($table->operation) {
                case Table::ADD:
                    $instruction->setOperation(Table::ADD);
                    $this->createTable($table, $instruction);
                    break;
                case Table::UPDATE:
                    $instruction->setOperation(Table::UPDATE);
                    $this->updateTable($table, $instruction);
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

            switch ($instruction->operation) {
                case Table::ADD:
                    $query = "CREATE TABLE " . $instruction->tableName . "(";
                    $query .= implode(", ", $instruction->instructions) . ($instruction->constraints == [] ? "" : ", ");
                    $query .= implode(", ", $instruction->constraints);
                    $query .= ");";
                    array_push($queries, $query);
                    break;
                case Table::UPDATE:
                    $query = "ALTER TABLE " . $instruction->tableName . " ";
                    $query .= implode(", ", $instruction->instructions) . ($instruction->constraints == [] ? "" : ", ");
                    $query .= implode(", ", $instruction->constraints) . ";";
                    array_push($queries, $query);
                    break;
                default:
                    var_dump("DEFAULT GET QUERIES");
                    break;
            }
        }

        return $queries;
    }

    private function updateTable(Table $table, Instruction $instruction)
    {
        foreach ($table->columns as $column) {

            if ($column instanceof ColumnUpdateInterface) {
                
                if($column->cascadeOnDelete || $column->cascadeOnUpdate){
                    throw new Exception("You can't update onDeleteCascade or onUpdateCascade. What you need to do is to drop the constraint then the table and create a
                    column from scratch with your olds properties and your new onDeleteCascade/onUpdateCascade");
                }

                $this->dropConstraint($column, $instruction);
                $this->updateType($column, $instruction);
                $this->updateNullable($column, $instruction);
                $this->updateDefault($column, $instruction);
                $this->addPrimaryConstraint($column, $instruction);
                $this->addForeignConstraint($column, $instruction);

                if ($column->dropColumn) {
                    $instruction->set("DROP COLUMN " . $column->name);
                }

            } elseif ($column instanceof ColumnCreateInterface) {
                $addColumn = "ADD COLUMN ";
                switch ($column->type) {
                    case ColumnType::$smallint:
                    case ColumnType::$int:
                    case ColumnType::$bigint:
                        $addColumn .= $this->integerNumeric($column, $instruction);
                        break;

                    case ColumnType::$char:
                    case ColumnType::$varchar:
                        $addColumn .= $this->textVariableLength($column, $instruction);
                        break;

                    case ColumnType::$decimal:
                         $addColumn .= $this->getNameAndType($column) . "(" . $column->precision . "," . $column->scale . ")" . $this->getDefaultOrNullable($column);
                        break;

                    default:
                        $addColumn .= $this->defaultInstruction($column);
                        break;
                }

                $instruction->set($addColumn);

                if($column->primaryKeyConstraint != null){
                    $this->addPrimaryConstraint($column, $instruction);
                }
                if($column->foreignKeyConstraint != null){
                    $this->addForeignConstraint($column, $instruction);
                }
            }
        }
    }

    private function updateDefault(ColumnBase $column, Instruction $instruction)
    {
        if ($column->default != null) {
            $instruction->set("ALTER COLUMN " . $column->name . " SET DEFAULT " . $column->default);
        }
    }

    private function addPrimaryConstraint(ColumnBase $column, Instruction $instruction)
    {
        if ($column->primaryKeyConstraint != null) {
            $instruction->setConstraint("ADD CONSTRAINT " . $column->primaryKeyConstraint . " PRIMARY KEY(" . $column->name . ")");
        }
    }

    private function addForeignConstraint(ColumnBase $column, Instruction $instruction)
    {
        if ($column->foreignKeyConstraint != null) {
            $instruction->setConstraint(
                "ADD CONSTRAINT " .
                    $column->foreignKeyConstraint .
                    " FOREIGN KEY(" . $column->name . ") REFERENCES " . $column->foreignKeyTableReference . "(" . $column->foreignKeyColumnReference . ")" .
                    ($column->cascadeOnDelete ? " ON DELETE CASCADE" : "") .
                    ($column->cascadeOnUpdate ? " ON UPDATE CASCADE" : "")
            );
        }
    }

    private function dropConstraint(ColumnBase $column, Instruction $instruction)
    {
        if ($column->dropPk) {
            $instruction->set("DROP CONSTRAINT " . $column->primaryKeyConstraint);
            $column->primaryKeyConstraint = null;
        }
        if ($column->dropFk) {
            $instruction->set("DROP CONSTRAINT " . $column->foreignKeyConstraint);
            $column->foreignKeyConstraint = null;
        }
    }

    private function updateType(ColumnBase $column, Instruction $instruction)
    {
        if ($column->type != null) {
            $typeParameter = "";
            if($column->type == ColumnType::$decimal){
                $typeParameter = "(" . $column->precision . "," . $column->scale . ")";
            }else if(($column->type == ColumnType::$char || $column->type == ColumnType::$varchar) && $column->length != null){
                $typeParameter = "(" . $column->length . ")";
            }
            $instruction->set("ALTER COLUMN " . $column->name . " TYPE " . $column->type . $typeParameter);
        }
    }

    private function updateNullable(ColumnBase $column, Instruction $instruction)
    {

        if ($column->nullable == true && $column->nullable != null) {
            $instruction->set("ALTER COLUMN " . $column->name . " DROP NOT NULL");
        } else if ($column->nullable == false && $column->nullable != null) {
            $instruction->set("ALTER COLUMN " . $column->name . " SET NOT NULL");
        }
    }

    private function createTable(Table $table, Instruction $instruction)
    {
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case ColumnType::$smallint:
                case ColumnType::$int:
                case ColumnType::$bigint:
                    $instruction->set($this->integerNumeric($column, $instruction));
                    break;

                case ColumnType::$char:
                case ColumnType::$varchar:
                    $instruction->set($this->textVariableLength($column, $instruction));
                    break;

                case ColumnType::$decimal:
                    $instruction->set(
                        $this->getNameAndType($column) .
                            "(" . $column->precision . "," . $column->scale . ")" . $this->getDefaultOrNullable($column)
                    );
                    break;

                default:
                    $instruction->set($this->defaultInstruction($column));
                    break;
            }

            $this->setConstraint($column, $instruction);
        }
    }

    private function defaultInstruction(ColumnBase $column)
    {
        return $this->getNameAndType($column) . $this->getDefaultOrNullable($column);
    }

    private function integerNumeric(ColumnBase $column)
    {
        if ($column->autoIncrement) {
            switch ($column->type) {
                case ColumnType::$smallint:
                    return $column->name . " SMALLSERIAL";
                    break;

                case ColumnType::$int:
                    return $column->name . " SERIAL";
                    break;

                case ColumnType::$bigint:
                    return $column->name . " BIGSERIAL";
                    break;
                default:
                    throw new Exception("Default edge case");
                    break;
            }
        } else {
            return $this->defaultInstruction($column);
        }
    }

    private function textVariableLength(ColumnBase $column)
    {
        $lengthParameter = "";
        if ($column->length != null) {
            $lengthParameter = "(" . $column->length . ")";
        }

        return $this->getNameAndType($column) . $lengthParameter . $this->getDefaultOrNullable($column);
    }

    private function setConstraint(ColumnBase $column, Instruction $instruction)
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

    private function getDefaultOrNullable(ColumnBase $column)
    {
        // $column
        return (($column->nullable == true) ? " NULL" : " NOT NULL") .
            ($column->default != null ?
                ($column->withTimeZone ? " WITH TIME ZONE" : "")
                . " DEFAULT " . $column->default : ""
            );
    }

    private function getNameAndType(ColumnBase $column)
    {
        return $column->name . " " . $column->type;
    }
}
