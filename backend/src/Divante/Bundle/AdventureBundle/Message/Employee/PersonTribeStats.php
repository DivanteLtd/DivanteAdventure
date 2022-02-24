<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class PersonTribeStats
{
    use ObjectTrait;

    private string $tribeName;
    private int $employeeId;
    private \DateTime $data;
    private string $employmentStatus;

    public function __construct(string $tribeName, int $employeeId, \DateTime $data, string $employmentStatus)
    {
        $this->tribeName = $tribeName;
        $this->employeeId = $employeeId;
        $this->data = $data;
        $this->employmentStatus = $employmentStatus;
    }

    public function getTribeName(): string
    {
        return $this->tribeName;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getData(): \DateTime
    {
        return $this->data;
    }

    public function getEmploymentStatus(): string
    {
        return $this->employmentStatus;
    }
}
