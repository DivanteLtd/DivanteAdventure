<?php

namespace Divante\Bundle\AdventureBundle\Filters\Employee;

use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;

/**
 * Filter EmployeeOnDelegationToday returns true if and only if passed employee is on trip today.
 * @package Divante\Bundle\AdventureBundle\Filters\Employee
 */
class EmployeeOnDelegationToday extends AbstractWorkLocationFilter
{
    protected function getType(): int
    {
        return EmployeeWorkLocation::TYPE_DELEGATION;
    }
}
