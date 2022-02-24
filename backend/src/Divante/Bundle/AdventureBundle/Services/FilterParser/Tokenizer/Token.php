<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Column;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\FunctionSeparator;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Parenthesis;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\PrimitiveValue;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\SqlFunction;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Structure;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\TwoArgOperand;

class Token
{
    public const STRING = 'STRING';
    public const EQUALS = 'EQUALS';
    public const AND = 'AND';
    public const OR = 'OR';
    public const LIKE = 'LIKE';
    public const ID = 'ID';
    public const LEFT_PARENTHESIS = 'LEFT_PARENTHESIS';
    public const RIGHT_PARENTHESIS = 'RIGHT_PARENTHESIS';
    public const FUNCTION = 'FUNCTION';
    public const FUNCTION_SEPARATOR = 'FUNCTION_SEPARATOR';

    /**
     * Every regex should start and end with surrounding character (i.e. /). After opening
     * character ^ symbol should be set.
     *
     * Run from top to bottom.
     */
    public const RULES = [
        [
            'name' => self::STRING,
            'regex' => '/^"([^"\\\\]*(\\\\.[^"\\\\]*)*)"/',
            'substring_start' => 1,
            'substring_end' => 1,
        ], [
            'name' => self::STRING,
            'regex' => "/^'([^'\\\\]*(\\\\.[^'\\\\]*)*)'/",
            'substring_start' => 1,
            'substring_end' => 1,
        ], [
            'name' => self::EQUALS,
            'regex' => '/^=/',
        ],[
            'name' => self::AND,
            'regex' => '/^(and|&{1,2})/i',
        ],[
            'name' => self::OR,
            'regex' => '/^(or|\|{1,2})/i',
        ],[
            'name' => self::FUNCTION,
            'regex' => '/^(not|in)/i'
        ],[
            'name' => self::LIKE,
            'regex' => '/^like/i',
        ],[
            'name' => self::ID,
            'regex' => '/^[a-zA-Z0-9_]+/',
        ],[
            'name' => self::LEFT_PARENTHESIS,
            'regex' => '/^\(/'
        ],[
            'name' => self::RIGHT_PARENTHESIS,
            'regex' => '/^\)/'
        ],[
            'name' => self::FUNCTION_SEPARATOR,
            'regex' => '/^,/'
        ]
    ];

    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param Field[] $fields
     * @return Structure
     */
    public function convertToBaseStructure(array $fields) : Structure
    {
        switch ($this->name) {
            case Token::STRING:
                return new PrimitiveValue($this);
            case Token::EQUALS:
            case Token::AND:
            case Token::OR:
            case Token::LIKE:
                return new TwoArgOperand($this);
            case Token::ID:
                return new Column($this, $fields);
            case Token::LEFT_PARENTHESIS:
            case Token::RIGHT_PARENTHESIS:
                return new Parenthesis($this);
            case Token::FUNCTION:
                return new SqlFunction($this);
            case Token::FUNCTION_SEPARATOR:
                return new FunctionSeparator();
            default:
                throw new \Exception("Unrecognized token type in convertToBaseStructure: ".$this->name);
        }
    }
}
