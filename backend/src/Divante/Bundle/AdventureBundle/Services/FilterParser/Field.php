<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser;

/**
 * Class Field represents corelation between field accessible in ParserInterface and column in table
 * @package Divante\Bundle\AdventureBundle\Services\FilterParser
 */
class Field
{
    private string $fieldName;
    private string $tableName;
    private string $columnName;

    public function __construct(string $fieldName, string $tableName, string $columnName)
    {
        $this->fieldName = $fieldName;
        $this->tableName = $tableName;
        $this->columnName = $columnName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }
}
