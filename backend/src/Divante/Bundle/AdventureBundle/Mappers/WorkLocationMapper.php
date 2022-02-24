<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;

class WorkLocationMapper
{
    /**
     * @param EmployeeWorkLocation $employeeWorkLocation
     * @return array<string,mixed>
     */
    public function __invoke(EmployeeWorkLocation $employeeWorkLocation): array
    {
        return [
            'id' => $employeeWorkLocation->getId(),
            'employeeId' => $employeeWorkLocation->getEmployeeId(),
            'type' => $employeeWorkLocation->getType(),
            'date' => $employeeWorkLocation->getDate(),
        ];
    }
}
