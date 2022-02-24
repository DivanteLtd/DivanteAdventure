<?php

namespace Tests\AdventureBundle\Services\FilterParser\Parser;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class InFunctionParsingTest extends ParserTestCase
{
    public function testColumnInValues() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
        ];
        $input = "firstName IN(\"John\", \"Alex\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("(employee.name IN(\"John\", \"Alex\"))", $result);
    }

    public function testNegation() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
        ];
        $input = "NOT(firstName IN(\"John\", \"Alex\"))";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("NOT((employee.name IN(\"John\", \"Alex\")))", $result);
    }

    public function testWithAnd() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "firstName IN(\"John\", \"Alex\") AND lastName = \"Doe\"";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("((employee.name IN(\"John\", \"Alex\")) AND (employee.lastName = \"Doe\"))", $result);
    }

    public function testWithOrAndNegation() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "NOT(firstName IN(\"John\", \"Alex\") OR lastName = \"Doe\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("NOT(((employee.name IN(\"John\", \"Alex\")) OR (employee.lastName = \"Doe\")))", $result);
    }
}
