<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class TribeMasterEmployeeMapper extends AbstractEmployeeMapper
{
    private ManagerHREmployeeMapper $managerHRMapper;

    public function __construct(ManagerHREmployeeMapper $managerHRMapper)
    {
        $this->managerHRMapper = $managerHRMapper;
    }

    /** @inheritDoc */
    public function mapEmployeeToJson(Employee $entity): array
    {
        return array_merge(
            $this->managerHRMapper->mapEmployeeToJson($entity),
            [
                'emergencyFirstName' => $entity->getEmergencyFirstName(),
                'emergencyLastName' => $entity->getEmergencyLastName(),
                'emergencyAddress' => $entity->getEmergencyAddress(),
                'emergencyPhone' => $entity->getEmergencyPhone(),
                "privatePhone" => $entity->getPrivatePhone(),
            ]
        );
    }
}
