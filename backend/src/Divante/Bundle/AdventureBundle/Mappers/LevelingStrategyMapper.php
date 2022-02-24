<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Level;
use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Entity\Position;

class LevelingStrategyMapper implements Mapper
{

    /**
     * @param LevelingStrategy $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'createdAt' => $entity->getCreatedAt()->getTimestamp(),
            'updatedAt' => $entity->getUpdatedAt()->getTimestamp(),
            'levels' => $entity->getLevels()->map(
                function (Level $level) {
                    return [
                        'id' => $level->getId(),
                        'name' => $level->getName(),
                        'employeeCount' => $level->getEmployees()->count(),
                        'priority' => $level->getPriority()
                    ];
                }
            )->toArray(),
            'positions' => $entity->getPositions()->map(
                function (Position $position) {
                    return [
                        'id' => $position->getId(),
                        'name' => $position->getName(),
                        'employeeCount' => $position->getEmployees()->count()
                    ];
                }
            )->toArray()
        ];
    }
}
