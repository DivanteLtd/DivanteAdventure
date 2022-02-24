<?php


namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;

class CriteriaMapper implements Mapper
{

    /**
     * @param DataProcessingCriteria $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'id' => $entity->getId(),
            'namePl' => $entity->getNamePl(),
            'nameEn' => $entity->getNameEn()
        ];
    }

    /**
     * @param DataProcessingCriteria $criteria
     * @return array<string,mixed>
     */
    public function __invoke(DataProcessingCriteria $criteria): array
    {
        return $this->mapEntity($criteria);
    }
}
