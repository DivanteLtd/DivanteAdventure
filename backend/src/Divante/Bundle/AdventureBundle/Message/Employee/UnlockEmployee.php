<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UnlockEmployee
{
    use ObjectTrait;
    /** @var int */
    private $employeeId;

    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function getEmployeeId() : int
    {
        return $this->employeeId;
    }
}
