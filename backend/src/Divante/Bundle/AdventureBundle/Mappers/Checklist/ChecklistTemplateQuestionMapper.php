<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;

class ChecklistTemplateQuestionMapper implements Mapper
{
    /**
     * @param ChecklistTemplateQuestion $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $data = [
            'id' => $entity->getId(),
            'namePl' => $entity->getNamePl(),
            'nameEn' => $entity->getNameEn(),
            'descriptionPl' => $entity->getDescriptionPl(),
            'descriptionEn' => $entity->getDescriptionEn(),
            'possibleStatuses' => $entity->getPossibleStatuses(),
        ];
        $employee = $entity->getResponsible();
        if (!is_null($employee)) {
            $data['responsible'] = [
                'id' => $employee->getId(),
                'photo' => $employee->getPhoto(),
                'name' => $employee->getName(),
                'lastName' => $employee->getLastName(),
            ];
        }
        return $data;
    }

    /**
     * @param ChecklistTemplateQuestion $entity
     * @return array<string,mixed>
     */
    public function __invoke(ChecklistTemplateQuestion $entity) : array
    {
        return $this->mapEntity($entity);
    }
}
