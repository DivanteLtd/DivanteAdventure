<?php

namespace Divante\Bundle\AdventureBundle\Events;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;

class WorkLocationStatusEvent extends AbstractSlackStatusEvent
{
    private EmployeeWorkLocation $workLocation;
    private Employee $employee;

    public function __construct(EmployeeWorkLocation $workLocation, Employee $employee)
    {
        $this->workLocation = $workLocation;
        $this->employee = $employee;
    }

    public function isRequestContainingToday(): bool
    {
        return true;
    }

    public function isEventAcceptingRequest(): bool
    {
        return true;
    }

    public function getTribeNotificationType(): ?int
    {
        return $this->workLocation->getType();
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }
}
