<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.12.18
 * Time: 14:17
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;

class EmployeeProjectMapper implements Mapper
{

    /**
     * @param EmployeeProject $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            "id" => $entity->getId(),
            "employeeId" => $entity->getEmployee()->getId(),
            "projectId" => $entity->getProject()->getId(),
            "overtime" => false
        ];
    }
}
