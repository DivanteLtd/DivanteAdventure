<?php

namespace Divante\Bundle\AdventureBundle\Query\Employee;

interface FirstEmployeeHiredDateQuery
{
    /** @return array<string,mixed> */
    public function getFirstHiredDate() :array;
}
