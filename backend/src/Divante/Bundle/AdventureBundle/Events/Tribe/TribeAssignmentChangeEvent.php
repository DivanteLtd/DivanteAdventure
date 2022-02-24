<?php

namespace Divante\Bundle\AdventureBundle\Events\Tribe;

use DateTime;
use Symfony\Component\EventDispatcher\Event;

class TribeAssignmentChangeEvent extends Event
{
    private int $employeeId;
    private ?int $oldTribeId;
    private ?int $newTribeId;
    private DateTime $date;

    public function __construct(int $employeeId, ?int $oldTribeId, ?int $newTribeId = null, ?DateTime $date = null)
    {
        $this->employeeId = $employeeId;
        $this->oldTribeId = $oldTribeId;
        $this->newTribeId = $newTribeId;
        $this->date = $date ?? new DateTime();
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getOldTribeId(): ?int
    {
        return $this->oldTribeId;
    }

    public function getNewTribeId(): ?int
    {
        return $this->newTribeId;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }
}
