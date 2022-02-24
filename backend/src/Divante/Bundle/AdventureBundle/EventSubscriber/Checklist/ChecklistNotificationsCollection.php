<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class ChecklistNotificationsCollection
{
    /** @var array<string,ChecklistNotificationEntry> */
    private array $entries = [];

    public function getForEmployee(Employee $employee): ChecklistNotificationEntry
    {
        /** @var ChecklistNotificationEntry $entry */
        $entry = $this->entries[$employee->getEmail()] ?? new ChecklistNotificationEntry($employee);
        $this->entries[$employee->getEmail()] = $entry;
        return $entry;
    }

    /** @return Employee[] */
    public function getEmployees(): array
    {
        return array_map(fn(ChecklistNotificationEntry $e) => $e->getEmployee(), $this->entries);
    }

    public function addNotification(Employee $employee, int $type): void
    {
        $this->getForEmployee($employee)->addType($type);
    }
}
