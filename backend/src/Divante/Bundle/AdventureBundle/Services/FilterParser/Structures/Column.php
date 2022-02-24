<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Exception\ParserException;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;

class Column implements Structure
{
    private Token $token;
    /** @var Field[] */
    private array $fields;

    /**
     * Column constructor.
     * @param Token $token
     * @param Field[] $fields
     */
    public function __construct(Token $token, array $fields)
    {
        $this->token = $token;
        $this->fields = $fields;
    }

    public function toSql(): string
    {
        foreach ($this->fields as $field) {
            if ($field->getFieldName() === $this->token->getValue()) {
                return $field->getTableName().'.'.$field->getColumnName();
            }
        }
        throw new ParserException("Unrecognized field ".$this->token->getValue());
    }
}
