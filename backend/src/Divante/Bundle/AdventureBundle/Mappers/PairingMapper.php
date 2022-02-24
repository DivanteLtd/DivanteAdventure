<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 04.01.19
 * Time: 09:44
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;

class PairingMapper
{
    /**
     * @param EmployeeOccupancy $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            "id" => $entity->getId(),
            "employeeId" => $entity->getProject()->getId(),
            "projectId" => $entity->getProject()->getId(),
            "timestamp" => $entity->getDate(),
            "secondsPerDay" => $entity->getOccupancy()

        ];
    }
}
