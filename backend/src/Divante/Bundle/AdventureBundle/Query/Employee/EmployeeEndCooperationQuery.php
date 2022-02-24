<?php

namespace Divante\Bundle\AdventureBundle\Query\Employee;

interface EmployeeEndCooperationQuery
{
    /** @return EmployeeEndCooperationView[] */
    public function getAll() :array;
}
