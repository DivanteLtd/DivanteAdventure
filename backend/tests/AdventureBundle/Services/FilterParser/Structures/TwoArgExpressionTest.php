<?php

namespace Tests\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Column;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\PrimitiveValue;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\TwoArgExpression;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\TwoArgOperand;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class TwoArgExpressionTest extends ParserTestCase
{
    public function testTwoArgExpression() : void
    {
        $fieldName = "RandomFieldName".rand(0, 10000);
        $tableName = "RandomTableName".rand(0, 10000);
        $columnName = "RandomColumnName".rand(0, 10000);
        $idToken = new Token(Token::ID, $fieldName);
        $field = new Field($fieldName, $tableName, $columnName);

        $val = "RandomValue".rand(0, 10000);

        $idStruct = new Column($idToken, [ $field ]);
        $valStruct = new PrimitiveValue(new Token(Token::STRING, $val));
        $operand = new TwoArgOperand(new Token(Token::EQUALS, '='));

        $expression = new TwoArgExpression($idStruct, $valStruct, $operand);
        $sql = $expression->toSql();

        $this->assertEquals("($tableName.$columnName = \"$val\")", $sql);
    }
}
