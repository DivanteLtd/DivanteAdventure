<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;

class ChecklistTemplateMapper implements Mapper
{

    /**
     * @param ChecklistTemplate $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'id' => $entity->getId(),
            'type' => $entity->getType(),
            'namePl' => $entity->getNamePl(),
            'nameEn' => $entity->getNameEn(),
            'createdAt' => $entity->getCreatedAt()->getTimestamp(),
            'updatedAt' => $entity->getUpdatedAt()->getTimestamp(),
        ];
    }

    /**
     * @param ChecklistTemplate $template
     * @return array<string,mixed>
     */
    public function __invoke(ChecklistTemplate $template) : array
    {
        return $this->mapEntity($template);
    }
}
