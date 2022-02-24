<?php

namespace Tests\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Tests\FoundationTestCase;

class ConfigEntryTest extends FoundationTestCase
{
    public function testId(): void
    {
        $entry = new ConfigEntry();
        $id = rand(0, 10000);
        $this->setId($entry, $id);
        $this->assertSame($id, $entry->getId());
    }

    public function testCreatedAt(): void
    {
        $entry = new ConfigEntry();
        $entry->setCreatedAt();
        $this->assertEqualsWithDelta(new DateTime(), $entry->getCreatedAt(), 5);
    }

    public function testReplacedAt(): void
    {
        $entry = new ConfigEntry();
        $entry->setReplacedAt();
        $this->assertEqualsWithDelta(new DateTime(), $entry->getReplacedAt(), 5);
    }

    public function testKey(): void
    {
        $entry = new ConfigEntry();
        $key = "RandomKey".rand(0, 10000);
        $entry->setKey($key);
        $this->assertSame($key, $entry->getKey());
    }

    public function testValue(): void
    {
        $entry = new ConfigEntry();
        $value = "RandomValue".rand(0, 10000);
        $entry->setValue($value);
        $this->assertSame($value, $entry->getValue());
    }

    public function testResponsible(): void
    {
        $entry = new ConfigEntry();
        $employee = new Employee();
        $entry->setResponsible($employee);
        $this->assertSame($employee, $entry->getResponsible());
    }
}
