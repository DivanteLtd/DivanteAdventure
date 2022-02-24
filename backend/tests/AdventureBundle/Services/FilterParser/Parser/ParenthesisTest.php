<?php

namespace Tests\AdventureBundle\Services\FilterParser\Parser;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class ParenthesisTest extends ParserTestCase
{
    public function testParensInNaturalPlace() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "firstName = \"John\" OR (firstName = \"Janina\" AND lastName = \"Doe\")";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals(
            "((employee.name = \"John\") OR ((employee.name = \"Janina\") AND (employee.lastName = \"Doe\")))",
            $result
        );
    }

    public function testParensInNonNaturalPlace() : void
    {
        $columnDefinitions = [
            new Field("firstName", "employee", "name"),
            new Field("lastName", "employee", "lastName"),
        ];
        $input = "(firstName = \"John\" OR firstName = \"Janina\") AND lastName = \"Doe\"";
        $result = $this->getParser()->parse($input, $columnDefinitions);
        $this->assertEquals(
            "(((employee.name = \"John\") OR (employee.name = \"Janina\")) AND (employee.lastName = \"Doe\"))",
            $result
        );
    }
}
