<?php

namespace Tests\AdventureBundle\Services\FilterParser\Parser;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class LogicalStructuresTest extends ParserTestCase
{
    public function testAnd() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "firstName = \"John\" AND lastName = \"Doe\"";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("((employee.name = \"John\") AND (employee.lastName = \"Doe\"))", $result);
    }

    public function testOr() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "firstName = \"John\" OR firstName = \"Janina\"";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals("((employee.name = \"John\") OR (employee.name = \"Janina\"))", $result);
    }

    public function testComplex() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "firstName = \"John\" OR firstName = \"Janina\" AND lastName = \"Doe\"";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals(
            "((employee.name = \"John\") OR ((employee.name = \"Janina\") AND (employee.lastName = \"Doe\")))",
            $result
        );
    }
}
