<?php

namespace Tests\AdventureBundle\Services\FilterParser\Parser;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class NotFunctionParsingTest extends ParserTestCase
{
    public function testNotEquals() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
        ];
        $input = "NOT(firstName = \"John\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("NOT((employee.name = \"John\"))", $result);
    }

    public function testNotLike() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
        ];
        $input = "NOT(firstName like \"%oh%\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("NOT((employee.name LIKE \"%oh%\"))", $result);
    }

    public function testNotStructure() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "NOT(firstName = \"John\" AND lastName = \"Doe\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("NOT(((employee.name = \"John\") AND (employee.lastName = \"Doe\")))", $result);
    }

    public function testNotInnerFunction() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
        ];
        $input = "not(NOT(firstName = \"John\"))";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("NOT(NOT((employee.name = \"John\")))", $result);
    }

    public function testCommasInNotFunction() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "NOT(firstName = \"John\", lastName = \"Doe\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("NOT((employee.name = \"John\"), (employee.lastName = \"Doe\"))", $result);
    }

    public function testUsageOfFunctionResultInTwoArgsOperation() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "firstName = \"John\" AND NOT(lastName = \"Doe\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("((employee.name = \"John\") AND NOT((employee.lastName = \"Doe\")))", $result);
    }
}
