<?php

namespace Tests\AdventureBundle\Services\FilterParser;

use Divante\Bundle\AdventureBundle\Services\FilterParser\Parser;
use Divante\Bundle\AdventureBundle\Services\FilterParser\ParserInterface;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Tokenizer;
use Tests\FoundationTestCase;

abstract class ParserTestCase extends FoundationTestCase
{
    public function getParser() : ParserInterface
    {
        return new Parser(
            $this->getTokenizer(),
        );
    }

    public function getTokenizer() : Tokenizer
    {
        return new Tokenizer();
    }
}
