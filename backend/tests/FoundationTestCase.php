<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

abstract class FoundationTestCase extends TestCase
{
    protected function setId($entity, int $id) : void
    {
        $this->setField($entity, 'id', $id);
    }

    protected function setField($entity, string $fieldName, $value) : void
    {
        $reflection = new \ReflectionObject($entity);
        if ($fieldName === 'responsible') {
            var_dump($reflection->getProperties());
        }
        $prop = $reflection->getProperty($fieldName);
        $prop->setAccessible(true);
        $prop->setValue($entity, $value);
    }
}
