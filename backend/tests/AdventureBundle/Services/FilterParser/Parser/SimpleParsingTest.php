<?php

namespace Tests\AdventureBundle\Services\FilterParser\Parser;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class SimpleParsingTest extends ParserTestCase
{
    public function testNoData() : void
    {
        $result = $this->getParser()->parse('', []);
        $this->assertEquals('', $result);
    }

    public function testSimpleString() : void
    {
        $result = $this->getParser()->parse('"abc"', []);
        $this->assertEquals('"abc"', $result);
    }

    public function testComparision() : void
    {
        $columnDefinition = new Field("firstName", "employee", "name");
        $input = "firstName = \"Norbert\"";
        $result = $this->getParser()->parse($input, [ $columnDefinition ]);
        $this->assertEquals("(employee.name = \"Norbert\")", $result);
    }

    public function testLike() : void
    {
        $columnDefinition = new Field("firstName", "employee", "name");
        $input = "firstName like \"%rbe%\"";
        $result = $this->getParser()->parse($input, [ $columnDefinition ]);
        $this->assertEquals("(employee.name LIKE \"%rbe%\")", $result);
    }
}
