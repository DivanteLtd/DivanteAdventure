<?php

namespace Tests\AdventureBundle\Services\Structure;

use Tests\FoundationTestCase;

abstract class AbstractQueueTestBase extends FoundationTestCase
{
    protected function generateRandomElement() : string
    {
        return "RandomElement".rand(0, 10000);
    }
}
