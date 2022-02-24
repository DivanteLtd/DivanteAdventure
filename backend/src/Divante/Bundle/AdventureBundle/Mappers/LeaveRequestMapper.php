<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 14:37
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Entity\SickLeaveAttachment;
use Divante\Bundle\AdventureBundle\Mappers\Employee\AbstractEmployeeMapper;
use Divante\Bundle\AdventureBundle\Mappers\Employee\AdministratorEmployeeMapper;

class LeaveRequestMapper implements Mapper
{
    private AbstractEmployeeMapper $employeeMapper;

    public function __construct(AdministratorEmployeeMapper $employeeMapper)
    {
        $this->employeeMapper = $employeeMapper;
    }

    /**
     * @param LeaveRequest $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $days = [];
        /** @var LeaveRequestDay $requestDay */
        foreach ($entity->getRequestDays() as $requestDay) {
            $days[] = [
                'id' => $requestDay->getId(),
                'type' => $requestDay->getType(),
                'status' => $requestDay->getStatus(),
                'date' => $requestDay->getDate()->format('Y-m-d'),
                'hours' => $requestDay->getHours()
            ];
        }
        $attachments = [];
        /** @var SickLeaveAttachment $attachment */
        foreach ($entity->getAttachments() as $attachment) {
            $attachments[] = [
                'id' => $attachment->getId(),
                'name' => $attachment->getName(),
            ];
        }
        $manager = $entity->getManager();
        $mappedManager = is_null($manager) ? null : $this->employeeMapper->mapEmployeeToJson($manager);
        return [
            'id' => $entity->getId(),
            'days' => $days,
            'manager' => $mappedManager,
            'employee' => $this->employeeMapper->mapEmployeeToJson($entity->getPeriod()->getEmployee()),
            'periodId' => $entity->getPeriod()->getId(),
            'status' => $entity->getStatus(),
            'comment' => $entity->getComment(),
            'createdAt' => $entity->getCreatedAt(),
            'acceptedAt' => $entity->getAcceptedAt(),
            'updatedAt' => $entity->getUpdatedAt(),
            'hidden' => $entity->isHidden(),
            'attachments' => $attachments
        ];
    }
}
