<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 29.03.19
 * Time: 09:25
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Entity\EvidenceOvertime;
use Divante\Bundle\AdventureBundle\Mappers\Employee\AbstractEmployeeMapper;
use Divante\Bundle\AdventureBundle\Mappers\Employee\AdministratorEmployeeMapper;

class EvidenceMapper implements Mapper
{
    private AbstractEmployeeMapper $employeeMapper;

    public function __construct(AdministratorEmployeeMapper $employeeMapper)
    {
        $this->employeeMapper = $employeeMapper;
    }

    /**
     * @param Evidence $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $array = [
            'id' => $entity->getId(),
            'employee' => $this->employeeMapper->mapEmployeeToJson($entity->getEmployee()),
            'year' => $entity->getYear(),
            'month' => $entity->getMonth(),
            'status' => $entity->getOvertimeStatus(),
            'paidFreeHours' => $entity->getPaidFreeHours(),
            'workingHours' => $entity->getWorkingHours(),
            'unpaidFreeHours' => $entity->getUnpaidFreeHours(),
            'sickLeaveHours' => $entity->getSickLeaveHours(),
            'unavailabilityHours' => $entity->getUnavailabilityHours(),
        ];
        if (!is_null($entity->getCreatedAt())) {
            $array['createdAt'] = $entity->getCreatedAt()->getTimestamp();
        }
        if (!is_null($entity->getUpdatedAt())) {
            $array['updatedAt'] = $entity->getUpdatedAt()->getTimestamp();
        }
        if (!is_null($entity->getOvertimeManager())) {
            $array['overtimeManager'] = $this->employeeMapper->mapEmployeeToJson($entity->getOvertimeManager());
        }
        $overtimeEntries = $entity->getOvertimeEntries();
        $overtime = [];
        /** @var EvidenceOvertime $entry */
        foreach ($overtimeEntries as $entry) {
            $overtime[] = [
                'code' => $entry->getProjectCode(),
                'name' => $entry->getProjectName(),
                'percentage' => $entry->getPercentage(),
                'hours' => $entry->getHours(),
                'timeInfo' => $entry->getTimeInfo()
            ];
        }
        $array['overtimeEntries'] = $overtime;
        return $array;
    }
}
