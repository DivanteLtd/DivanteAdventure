<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 04.01.19
 * Time: 11:59
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;

class OccupancyMapper implements Mapper
{

    /**
     * @param EmployeeOccupancy $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'id'            => $entity->getId(),
            'employeeId'    => $entity->getEmployee()->getId(),
            'projectId'     => $entity->getProject()->getId(),
            'timestamp'     => $entity->getDate(),
            'secondsPerDay' => $entity->getOccupancy()
        ];
    }
}
