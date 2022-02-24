<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\News;

class NewsMapper implements Mapper
{

    /**
     * @param News $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $json = [
            'id' => $entity->getId(),
            'type' => $entity->getType(),
            'title' => $entity->getTitle(),
            'banner' => $entity->getBanner(),
        ];
        if ($entity->getType() === News::TYPE_MARKDOWN) {
            $json['desc'] = $entity->getDescription();
        }
        return $json;
    }

    /**
     * @param News $entity
     * @return array<string,mixed>
     */
    public function __invoke(News $entity) : array
    {
        return $this->mapEntity($entity);
    }
}
