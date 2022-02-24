<?php

namespace Tests\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Parenthesis;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;
use Tests\FoundationTestCase;

class ParenthesisTest extends FoundationTestCase
{
    public function testLeftParenthesisIsLeft() : void
    {
        $token = new Token(Token::LEFT_PARENTHESIS, '(');
        $structure = new Parenthesis($token);
        $this->assertTrue($structure->isLeft());
    }

    public function testLeftParenthesisIsNotRight() : void
    {
        $token = new Token(Token::LEFT_PARENTHESIS, '(');
        $structure = new Parenthesis($token);
        $this->assertFalse($structure->isRight());
    }

    public function testRightParenthesisIsNotLeft() : void
    {
        $token = new Token(Token::RIGHT_PARENTHESIS, ')');
        $structure = new Parenthesis($token);
        $this->assertFalse($structure->isLeft());
    }

    public function testRightParenthesisIsRight() : void
    {
        $token = new Token(Token::RIGHT_PARENTHESIS, ')');
        $structure = new Parenthesis($token);
        $this->assertTrue($structure->isRight());
    }
}
