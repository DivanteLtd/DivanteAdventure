<?php

namespace Divante\Bundle\AdventureBundle\Events\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\EventDispatcher\Event;

class AbstractChecklistEvent extends Event
{
    private Checklist $checklist;
    private Employee $user;

    public function __construct(Checklist $checklist, Employee $user)
    {
        $this->checklist = $checklist;
        $this->user = $user;
    }

    public function getChecklist(): Checklist
    {
        return $this->checklist;
    }

    public function getUser(): Employee
    {
        return $this->user;
    }
}
