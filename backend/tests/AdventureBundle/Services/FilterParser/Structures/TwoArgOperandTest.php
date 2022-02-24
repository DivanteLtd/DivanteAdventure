<?php

namespace Tests\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\TwoArgOperand;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class TwoArgOperandTest extends ParserTestCase
{
    public function testAnd() : void
    {
        $token = new Token(Token::AND, '&&');
        $operand = new TwoArgOperand($token);
        $this->assertEquals("AND", $operand->toSql());
    }

    public function testOr() : void
    {
        $token = new Token(Token::OR, '||');
        $operand = new TwoArgOperand($token);
        $this->assertEquals("OR", $operand->toSql());
    }

    public function testEquals() : void
    {
        $token = new Token(Token::EQUALS, '=');
        $operand = new TwoArgOperand($token);
        $this->assertEquals("=", $operand->toSql());
    }

    public function testLike() : void
    {
        $token = new Token(Token::LIKE, 'like');
        $operand = new TwoArgOperand($token);
        $this->assertEquals("LIKE", $operand->toSql());
    }
}
