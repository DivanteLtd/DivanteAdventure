<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Level;

class LevelMapper implements Mapper
{

    /**
     * @param Level $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $strategyMapper = new LevelingStrategyMapper();
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'createdAt' => $entity->getCreatedAt(),
            'updatedAt' => $entity->getUpdatedAt(),
            'employeeCount' => $entity->getEmployees()->count(),
            'priority' => $entity->getPriority(),
            'strategy' => $strategyMapper->mapEntity($entity->getStrategy())
        ];
    }
}
