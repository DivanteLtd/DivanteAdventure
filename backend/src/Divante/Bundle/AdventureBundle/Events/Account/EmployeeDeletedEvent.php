<?php

namespace Divante\Bundle\AdventureBundle\Events\Account;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\EventDispatcher\Event;

class EmployeeDeletedEvent extends Event
{
    private Employee $removed;
    private ?Employee $removing;

    public function __construct(Employee $removed, ?Employee $removing)
    {
        $this->removed = $removed;
        $this->removing = $removing;
    }

    public function getRemoved(): Employee
    {
        return $this->removed;
    }

    public function getRemoving(): ?Employee
    {
        return $this->removing;
    }
}
