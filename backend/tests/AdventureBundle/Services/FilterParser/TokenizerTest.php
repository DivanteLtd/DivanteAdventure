<?php

namespace Tests\AdventureBundle\Services\FilterParser;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;

class TokenizerTest extends ParserTestCase
{
    public function testEmptyString() : void
    {
        $result = $this->getTokenizer()->tokenize("");
        $this->assertCount(0, $result);
    }

    public function testSpacesOnly() : void
    {
        $result = $this->getTokenizer()->tokenize("    \t    \n");
        $this->assertCount(0, $result);
    }

    public function testId() : void
    {
        $someColumnName = "someColumnName";
        $input = "    $someColumnName  \n\n   ";
        $result = $this->getTokenizer()->tokenize($input);
        $this->assertCount(1, $result);
        $this->assertEquals(Token::ID, $result[0]->getName());
        $this->assertEquals($someColumnName, $result[0]->getValue());
    }

    public function testMultipleIds() : void
    {
        $firstColumn = "FirstColumnName";
        $secondColumn = "SecondColumnName";
        $input = "  \n   \t  $firstColumn   \n   \t   $secondColumn   \n   \t   ";
        $result = $this->getTokenizer()->tokenize($input);
        $this->assertCount(2, $result);
        $this->assertEquals(Token::ID, $result[0]->getName());
        $this->assertEquals($firstColumn, $result[0]->getValue());
        $this->assertEquals(Token::ID, $result[1]->getName());
        $this->assertEquals($secondColumn, $result[1]->getValue());
    }

    public function testStringLiteral() : void
    {
        $input = "   \"abc'def\\\"ghi\"     jkl  ";
        $result = $this->getTokenizer()->tokenize($input);
        $this->assertCount(2, $result);
        $this->assertEquals(Token::STRING, $result[0]->getName());
        $this->assertEquals("abc'def\"ghi", $result[0]->getValue());
        $this->assertEquals(Token::ID, $result[1]->getName());
        $this->assertEquals('jkl', $result[1]->getValue());
    }

    public function testStringLiteralWithApostrophe() : void
    {
        $input = "   'abc\\'def\"ghi'     jkl  ";
        $result = $this->getTokenizer()->tokenize($input);
        $this->assertCount(2, $result);
        $this->assertEquals(Token::STRING, $result[0]->getName());
        $this->assertEquals("abc'def\"ghi", $result[0]->getValue());
        $this->assertEquals(Token::ID, $result[1]->getName());
        $this->assertEquals('jkl', $result[1]->getValue());
    }

    public function testKeyWords() : void
    {
        $input = "\"and\"and\"or\"or";
        $result = $this->getTokenizer()->tokenize($input);
        $this->assertCount(4, $result);
        $this->assertEquals(Token::STRING, $result[0]->getName());
        $this->assertEquals("and", $result[0]->getValue());
        $this->assertEquals(Token::AND, $result[1]->getName());
        $this->assertEquals(Token::STRING, $result[2]->getName());
        $this->assertEquals("or", $result[2]->getValue());
        $this->assertEquals(Token::OR, $result[3]->getName());
    }

    public function testSpecialCharacters() : void
    {
        $input = "   &    |  =   &&    ||  ";
        $result = $this->getTokenizer()->tokenize($input);
        $this->assertCount(5, $result);
        $this->assertEquals(Token::AND, $result[0]->getName());
        $this->assertEquals(Token::OR, $result[1]->getName());
        $this->assertEquals(Token::EQUALS, $result[2]->getName());
        $this->assertEquals(Token::AND, $result[3]->getName());
        $this->assertEquals(Token::OR, $result[4]->getName());
    }
}
