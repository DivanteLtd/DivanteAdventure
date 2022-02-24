<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;

class TwoArgOperand implements Structure
{
    private Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function getTokenType() : string
    {
        return $this->token->getName();
    }

    public function getPrecendence() : int
    {
        switch ($this->token->getName()) {
            case Token::EQUALS:
            case Token::LIKE:
                return 7;
            case Token::AND:
                return 11;
            case TOKEN::OR:
                return 12;
            default:
                return -1;
        }
    }

    public function toSql(): string
    {
        switch ($this->token->getName()) {
            case Token::EQUALS:
                return '=';
            case Token::AND:
                return 'AND';
            case TOKEN::OR:
                return 'OR';
            case Token::LIKE:
                return 'LIKE';
            default:
                return '';
        }
    }
}
