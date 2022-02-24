<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 10:46
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Mappers\Employee\AbstractEmployeeMapper;
use Divante\Bundle\AdventureBundle\Mappers\Employee\AdministratorEmployeeMapper;
use Divante\Bundle\AdventureBundle\Mappers\Employee\UserEmployeeMapper;

class PeriodMapper implements Mapper
{
    private AbstractEmployeeMapper $employeeMapper;
    private LeaveRequestMapper $requestMapper;

    public function __construct(AdministratorEmployeeMapper $employeeMapper, LeaveRequestMapper $requestMapper)
    {
        $this->employeeMapper = $employeeMapper;
        $this->requestMapper = $requestMapper;
    }

    /**
     * @param LeavePeriod $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $requests = [];
        /** @var LeaveRequest $request */
        foreach ($entity->getRequests() as $request) {
            $requests[] = $this->requestMapper->mapEntity($request);
        }

        return [
            'id' => $entity->getId(),
            'employee' => $this->employeeMapper->mapEmployeeToJson($entity->getEmployee()),
            'dateTo' => $entity->getDateTo()->format('Y-m-d'),
            'dateFrom' => $entity->getDateFrom()->format('Y-m-d'),
            'freeDays' => $entity->getFreedays(),
            'sickLeaveDays' => $entity->getSickLeaveDays(),
            'comment' => $entity->getCommentSystem(),
            'requests' => $requests,
        ];
    }

    /**
     * @param LeavePeriod $period
     * @return array<string,mixed>
     */
    public function __invoke(LeavePeriod $period) : array
    {
        return $this->mapEntity($period);
    }
}
