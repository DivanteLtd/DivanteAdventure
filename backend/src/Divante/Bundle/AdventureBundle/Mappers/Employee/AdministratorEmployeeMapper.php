<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Services\EmployeeRequirement;

class AdministratorEmployeeMapper extends AbstractEmployeeMapper
{
    private TribeMasterEmployeeMapper $tribeMasterEmployeeMapper;
    private EmployeeRequirement $requirement;

    public function __construct(TribeMasterEmployeeMapper $tribeMasterEmployeeMapper, EmployeeRequirement $requirement)
    {
        $this->tribeMasterEmployeeMapper = $tribeMasterEmployeeMapper;
        $this->requirement = $requirement;
    }

    /** @inheritDoc */
    public function mapEmployeeToJson(Employee $entity): array
    {
        return array_merge(
            $this->tribeMasterEmployeeMapper->mapEmployeeToJson($entity),
            [
                'locked' => $entity->isPinLocked(),
                'slackStatus' => $entity->getSlackStatus(),
                'agreementsRequired' => !$this->requirement->hasRequiredAgreements($entity),
                'hasSetPin' => $this->requirement->hasSetPin($entity),
                'language' => $entity->getLanguage(),
                'nip' => $entity->getNip(),
                'firmName' => $entity->getFirmName(),
                'firmAddress' => $entity->getFirmAddress(),
                'employee_code' => $entity->getEmployeeCode()
            ]
        );
    }
}
