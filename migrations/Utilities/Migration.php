<?php

namespace Migrations\Utilities;

use Migrations\Utilities\Column;

abstract class Migration
{
    /**
     * @var \Migrations\Utilities\Query[]
     */
    public array $queries = [];
    public abstract function up(Schema $schema);
    public abstract function down(Schema $schema);

    public function createQuery(Schema $schema)
    {
        $tables = $schema->getTables();

        foreach ($tables as $table) {
            $query = new Query();
            $columns = $table->columns;

            foreach ($columns as $column) {
                switch ($column->type) {
                    case ColumnType::$smallint:
                    case ColumnType::$int:
                    case ColumnType::$bigint:
                        $this->integerNumeric($column, $query);
                        break;

                    case ColumnType::$char:
                    case ColumnType::$varchar:
                        $this->textVariableLength($column, $query);
                        break;

                    case ColumnType::$decimal:
                        $query->setInstruction(
                            $this->getNameAndType($column) .
                                "(" . $column->precision . "," . $column->scale . ")" . $this->getDefaultOrNullable($column)
                        );
                        break;

                    default:
                        $this->defaultDataType($column, $query);
                        break;
                }

                $this->setConstraint($column, $query);
            }
            array_push($this->queries, $query);
        }
    }

    private function defaultDataType(Column $column, Query $query)
    {
        $query->setInstruction($this->getNameAndType($column) . $this->getDefaultOrNullable($column));
    }

    private function integerNumeric(Column $column, Query $query)
    {
        if ($column->autoIncrement) {
            switch ($column->type) {
                case ColumnType::$smallint:
                    $query->setInstruction($column->name . " SMALLSERIAL");
                    break;

                case ColumnType::$int:
                    $query->setInstruction($column->name . " SERIAL");
                    break;

                case ColumnType::$bigint:
                    $query->setInstruction($column->name . " BIGSERIAL");
                    break;
            }
        } else {
            $query->setInstruction($this->getNameAndType($column) . $this->getDefaultOrNullable($column));
        }
    }

    private function textVariableLength(Column $column, Query $query)
    {
        $lengthParameter = "";
        if ($column->length != null) {
            $lengthParameter = "(" . $column->length . ")";
        }

        $query->setInstruction($this->getNameAndType($column) . $lengthParameter . $this->getDefaultOrNullable($column));
    }

    private function setConstraint(Column $column, Query $query)
    {
        if ($column->primaryKeyConstraint != null) {
            $query->setConstraint("CONSTRAINT " . $column->primaryKeyConstraint . " PRIMARY KEY(" . $column->name . ")");
        }

        if ($column->foreignKeyConstraint != null) {
            $query->setConstraint(
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
