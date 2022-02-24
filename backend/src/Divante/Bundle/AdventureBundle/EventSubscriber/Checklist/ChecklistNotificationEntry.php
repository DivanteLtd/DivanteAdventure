<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class ChecklistNotificationEntry
{
    public const NOTIFICATION_SUBJECT = 1;
    public const NOTIFICATION_OWNER = 2;
    public const NOTIFICATION_TASK_RESPONSIBLE = 3;

    private Employee $employee;
    /** @var int[] */
    private array $types;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
        $this->types = [];
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function hasType(int $type): bool
    {
        return in_array($type, $this->types);
    }

    public function addType(int $type): void
    {
        if (!$this->hasType($type)) {
            $this->types[] = $type;
        }
    }
}
