<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Link;

class LinkMapper implements Mapper
{
    /**
     * @param Link $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'url' => $entity->getUrl(),
            'createdAt' => $entity->getCreatedAt(),
            'updatedAt' => $entity->getUpdatedAt(),
        ];
    }
}
