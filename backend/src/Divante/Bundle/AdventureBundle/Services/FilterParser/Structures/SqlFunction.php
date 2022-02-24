<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;

class SqlFunction implements Structure
{
    private Token $token;
    private int $argsCount;
    /** @var Structure[] */
    private array $arguments;

    public function __construct(Token $token)
    {
        $this->token = $token;
        $this->argsCount = 0;
        $this->arguments = [];
    }

    public function incrementArgumentsCount() : void
    {
        $this->argsCount++;
    }

    public function getArgumentsCount() : int
    {
        return $this->argsCount;
    }

    public function requiresValueBefore() : bool
    {
        $functionName = strtoupper($this->token->getValue());
        return $functionName === 'IN';
    }

    /** @param Structure[] $arguments */
    public function setArgumentValues(array $arguments) : void
    {
        $this->arguments = $arguments;
    }

    public function toSql(): string
    {
        /** @var string[] $argumentsSqlArray */
        $argumentsSqlArray = array_map(fn(Structure $s) => $s->toSql(), $this->arguments);
        $preValue = '';
        if ($this->requiresValueBefore()) {
            $preValue = array_shift($argumentsSqlArray).' ';
        }
        $argumentsSqlJoined = implode(', ', $argumentsSqlArray);
        $functionName = strtoupper($this->token->getValue());

        $sql = $preValue.$functionName.'('.$argumentsSqlJoined.')';
        if ($this->requiresValueBefore()) {
            return "($sql)";
        }
        return $sql;
    }
}
