<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;

class Parenthesis implements Structure
{
    private bool $isLeft;

    public function __construct(Token $token)
    {
        $this->isLeft = $token->getName() === Token::LEFT_PARENTHESIS;
    }

    public function isLeft() : bool
    {
        return $this->isLeft;
    }

    public function isRight() : bool
    {
        return !$this->isLeft;
    }

    public function toSql(): string
    {
        return ''; // should never be called
    }
}
