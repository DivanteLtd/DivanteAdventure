<?php

namespace Tests\AdventureBundle\Services\FilterParser\Structures;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Column;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;
use Tests\AdventureBundle\Services\FilterParser\ParserTestCase;

class ColumnTest extends ParserTestCase
{
    public function testColumn() : void
    {
        $fieldName = "RandomFieldName".rand(0, 10000);
        $tableName = "RandomTableName".rand(0, 10000);
        $columnName = "RandomColumnName".rand(0, 10000);
        $token = new Token(Token::ID, $fieldName);
        $field = new Field($fieldName, $tableName, $columnName);

        $column = new Column($token, [ $field ]);
        $sql = $column->toSql();
        $this->assertEquals($tableName.".".$columnName, $sql);
    }
}
