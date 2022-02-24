<?php

namespace Tests\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\PrimitiveValue;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\SqlFunction;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class SqlFunctionTest extends ParserTestCase
{
    public function testNoArguments() : void
    {
        $functionName = "RANDOM_FUNC_NAME_".rand(0, 10000);
        $token = new Token(Token::FUNCTION, $functionName);
        $sqlFunction = new SqlFunction($token);

        $expected = $functionName.'()';
        $this->assertEquals($expected, $sqlFunction->toSql());
    }

    public function testOneArgument() : void
    {
        $functionName = "RANDOM_FUNC_NAME_".rand(0, 10000);
        $token = new Token(Token::FUNCTION, $functionName);
        $sqlFunction = new SqlFunction($token);
        $arg1 = new PrimitiveValue(new Token(Token::STRING, 'RandomValue'.rand(0, 10000)));
        $sqlFunction->setArgumentValues([ $arg1 ]);

        $expected = $functionName.'('.$arg1->toSql().')';
        $this->assertEquals($expected, $sqlFunction->toSql());
    }

    public function testTwoArguments() : void
    {
        $functionName = "RANDOM_FUNC_NAME_".rand(0, 10000);
        $token = new Token(Token::FUNCTION, $functionName);
        $sqlFunction = new SqlFunction($token);
        $arg1 = new PrimitiveValue(new Token(Token::STRING, 'RandomValue'.rand(0, 10000)));
        $arg2 = new PrimitiveValue(new Token(Token::STRING, 'RandomValue'.rand(0, 10000)));
        $sqlFunction->setArgumentValues([ $arg1, $arg2 ]);

        $expected = $functionName.'('.$arg1->toSql().', '.$arg2->toSql().')';
        $this->assertEquals($expected, $sqlFunction->toSql());
    }

    public function testThreeArguments() : void
    {
        $functionName = "RANDOM_FUNC_NAME_".rand(0, 10000);
        $token = new Token(Token::FUNCTION, $functionName);
        $sqlFunction = new SqlFunction($token);
        $arg1 = new PrimitiveValue(new Token(Token::STRING, 'RandomValue'.rand(0, 10000)));
        $arg2 = new PrimitiveValue(new Token(Token::STRING, 'RandomValue'.rand(0, 10000)));
        $arg3 = new PrimitiveValue(new Token(Token::STRING, 'RandomValue'.rand(0, 10000)));
        $sqlFunction->setArgumentValues([ $arg1, $arg2, $arg3 ]);

        $expected = $functionName.'('.$arg1->toSql().', '.$arg2->toSql().', '.$arg3->toSql().')';
        $this->assertEquals($expected, $sqlFunction->toSql());
    }
}
