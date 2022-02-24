<?php

namespace Divante\Bundle\AdventureBundle\Events;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;

class RequestStatusEvent extends AbstractSlackStatusEvent
{
    private LeaveRequest $leaveRequest;
    private int $previousStatus;
    private bool $changedByManager;
    private bool $changedByUser;

    public function __construct(
        LeaveRequest $leaveRequest,
        int $previousStatus,
        bool $changedByUser,
        bool $changedByManager
    ) {
        $this->leaveRequest = $leaveRequest;
        $this->previousStatus = $previousStatus;
        $this->changedByManager = $changedByManager;
        $this->changedByUser = $changedByUser;
    }

    public function isRequestContainingToday() : bool
    {
        $currentDate = date('Y-m-d');
        return !$this->getRequest()
            ->getRequestDays()
            ->filter(
                function (LeaveRequestDay $day) use ($currentDate) {
                    return $day->getDate()->format('Y-m-d') === $currentDate;
                }
            )->isEmpty();
    }

    public function isEventAcceptingRequest() : bool
    {
        return $this->getPreviousStatus() === LeaveRequest::REQUEST_STATUS_PENDING
            && $this->getRequest()->getStatus() === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $this->changedByManager();
    }

    public function getTribeNotificationType() : ?int
    {
        return null;
    }

    public function getEmployee() : Employee
    {
        return $this->getRequest()->getEmployee();
    }

    public function getRequest() : LeaveRequest
    {
        return $this->leaveRequest;
    }

    public function getPreviousStatus() : int
    {
        return $this->previousStatus;
    }

    public function changedByManager() : bool
    {
        return $this->changedByManager;
    }

    public function changedByEmployee() : bool
    {
        return $this->changedByUser;
    }
}
