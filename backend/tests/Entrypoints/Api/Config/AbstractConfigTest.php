<?php

namespace Tests\Entrypoints\Api\Config;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Tests\Entrypoints\AbstractEntrypointTest;

abstract class AbstractConfigTest extends AbstractEntrypointTest
{
    public function generateConfigEntry(Employee $owner): ConfigEntry
    {
        $entry = new ConfigEntry();
        $entry->setKey("group.RandomKey".rand(0, 10000))
            ->setValue("RandomValue".rand(0, 10000))
            ->setGroup("RandomGroup".rand(0, 10000))
            ->setResponsible($owner)
            ->setCreatedAt();
        $this->em->persist($entry);
        return $entry;
    }
}
