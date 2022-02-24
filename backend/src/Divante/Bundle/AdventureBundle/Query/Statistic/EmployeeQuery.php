<?php

namespace Divante\Bundle\AdventureBundle\Query\Statistic;

interface EmployeeQuery
{
    /** @return EmployeeView[] */
    public function getAll(): array;
    /** @return EmployeeGeneralView[] */
    public function getAllGeneral(): array;
}
