<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class MinimalisticEmployeeMapper extends AbstractEmployeeMapper
{
    /** @inheritDoc */
    public function mapEmployeeToJson(Employee $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'lastName' => $entity->getLastName(),
            'photo' => $entity->getPhoto(),
            'email' => $entity->getEmail(),
        ];
    }
}
