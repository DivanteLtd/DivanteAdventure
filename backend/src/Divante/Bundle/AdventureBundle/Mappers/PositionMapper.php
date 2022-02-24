<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Position;

class PositionMapper implements Mapper
{

    /**
     * @param Position $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $strategyMapper = new LevelingStrategyMapper();
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'createdAt' => $entity->getCreatedAt()->getTimestamp(),
            'updatedAt' => $entity->getUpdatedAt()->getTimestamp(),
            'employeeCount' => $entity->getEmployees()->count(),
            'strategy' => $strategyMapper->mapEntity($entity->getStrategy())
        ];
    }

    /**
     * @param Position $position
     * @return array<string,mixed>
     */
    public function __invoke(Position $position) : array
    {
        return $this->mapEntity($position);
    }
}
