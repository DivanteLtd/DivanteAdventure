<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 21.12.18
 * Time: 11:29
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;

class LeaveDayMapper implements Mapper
{

    /**
     * @param LeaveRequestDay $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            "id" => $entity->getId(),
            "employeeId" => $entity->getEmployee()->getId(),
            "date" => $entity->getDate()->format('Y-m-d'),
            "type" => $entity->getPlannerDayType(),
            "notAccepted" => $entity->getRequest()->getStatus() === LeaveRequest::REQUEST_STATUS_PENDING,
            "planned" => $entity->getRequest()->getStatus() === LeaveRequest::REQUEST_STATUS_PLANNED,
        ];
    }
}
