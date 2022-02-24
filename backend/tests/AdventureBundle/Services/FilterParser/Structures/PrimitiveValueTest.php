<?php

namespace Tests\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\PrimitiveValue;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class PrimitiveValueTest extends ParserTestCase
{
    public function testPrimitiveValue() : void
    {
        $string = "RandomString".rand(0, 100000);
        $token = new Token(Token::STRING, $string);
        $val = new PrimitiveValue($token);

        $this->assertEquals("\"$string\"", $val->toSql());
    }
}
