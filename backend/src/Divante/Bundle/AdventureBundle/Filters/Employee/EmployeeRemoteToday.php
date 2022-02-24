<?php

namespace Divante\Bundle\AdventureBundle\Filters\Employee;

use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;

/**
 * Filter EmployeeRemoteToday returns true if and only if passed employee is working remotely today.
 * @package Divante\Bundle\AdventureBundle\Filters\Employee
 */
class EmployeeRemoteToday extends AbstractWorkLocationFilter
{
    protected function getType(): int
    {
        return EmployeeWorkLocation::TYPE_REMOTE;
    }
}
