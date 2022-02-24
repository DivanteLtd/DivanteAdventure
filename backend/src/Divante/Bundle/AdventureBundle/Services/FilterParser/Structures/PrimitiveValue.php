<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Exception\ParserException;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;

class PrimitiveValue implements Structure
{
    private Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function toSql(): string
    {
        if ($this->token->getName() === Token::STRING) {
            return '"'.$this->token->getValue().'"';
        }
        throw new ParserException("Unrecognized token in primitive value: ".json_encode($this->token));
    }
}
